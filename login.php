<?php
require_once 'includes/db.php';
require_once 'includes/mailer.php';
secure_session_start();

// If student is already logged in, send them to the homepage
if (isset($_SESSION['student_logged_in'])) {
    header('Location: ' . localized_url(''));
    exit;
}

$error = '';
$success = '';

// ==========================================
// 1. GOOGLE OAUTH 2.0 CONFIGURATION
// ==========================================
$google_client_id = app_secret('GOOGLE_CLIENT_ID');
$google_client_secret = app_secret('GOOGLE_CLIENT_SECRET');
$google_redirect_uri = app_secret('GOOGLE_REDIRECT_URI') ?: site_base_url() . 'login';
$google_enabled = $google_client_id !== '' && $google_client_secret !== '';

// Generate the Google Login URL
$google_login_url = '#';
if ($google_enabled) {
    if (!isset($_GET['code'])) {
        $_SESSION['google_oauth_state'] = bin2hex(random_bytes(16));
    }
    $google_login_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
        'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
        'redirect_uri' => $google_redirect_uri,
        'response_type' => 'code',
        'client_id' => $google_client_id,
        'access_type' => 'online',
        'state' => $_SESSION['google_oauth_state'] ?? '',
        'prompt' => 'select_account',
    ]);
}

// ==========================================
// 2. HANDLE GOOGLE CALLBACK (User returns from Google)
// ==========================================
if ($google_enabled && isset($_GET['code'])) {
    enforce_request_budget('student-oauth', 20, 600);
    $savedState = $_SESSION['google_oauth_state'] ?? '';
    unset($_SESSION['google_oauth_state']);
    if ($savedState === '' || !is_string($_GET['state'] ?? null) || !is_string($_GET['code']) || !hash_equals($savedState, $_GET['state'])) {
        $error = "Google Login Failed. Please refresh and try again.";
    } else {
    // Exchange the authorization code for an access token
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'code' => $_GET['code'],
        'client_id' => $google_client_id,
        'client_secret' => $google_client_secret,
        'redirect_uri' => $google_redirect_uri,
        'grant_type' => 'authorization_code'
    ]));
    $token_response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($token_response['access_token'])) {
        // Use access token to get user profile data
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token_response['access_token']]);
        $google_profile = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($google_profile['email']) && ($google_profile['verified_email'] ?? false) === true) {
            $email = filter_var($google_profile['email'], FILTER_VALIDATE_EMAIL);
            $name = trim($google_profile['name'] ?? 'Student');

            if (!$email) {
                $error = "Google Login Failed. Your Google account did not return a valid email.";
            } else {

            // Check if student already exists
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $student = $stmt->fetch();

            if ($student && ($student['account_status'] ?? 'active') === 'blocked') {
                $error = "This account is blocked. Contact support if you need it reviewed.";
            } elseif ($student) {
                // Log them in
                $pdo->prepare("UPDATE students SET email_verified = 1, email_verified_at = COALESCE(email_verified_at, NOW()) WHERE id = :id")->execute(['id' => $student['id']]);
                session_regenerate_id(true);
                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_name'] = $student['name'];
                header('Location: ' . localized_url(''));
                exit;
            } else {
                // Create new account for Google User (Generate random password since they use Google)
                $random_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO students (name, email, password, email_verified, email_verified_at, profile_public) VALUES (:name, :email, :password, 1, NOW(), 0)");
                $stmt->execute(['name' => $name, 'email' => $email, 'password' => $random_password]);
                defer_after_response(static fn() => send_welcome_mail($email, $name));
                
                session_regenerate_id(true);
                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_id'] = $pdo->lastInsertId();
                $_SESSION['student_name'] = $name;
                header('Location: ' . localized_url(''));
                exit;
            }
            }
        }
    } else {
        $error = "Google Login Failed. Please try again.";
    }
    }
}

// ==========================================
// 3. HANDLE MANUAL LOGIN / REGISTRATION (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();
    enforce_request_budget('student-auth', 20, 600);
    $action = $_POST['action'] ?? '';
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if ($action == 'login') {
        if (!$email || $password === '') {
            $error = "Please enter a valid email and password.";
        } else {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $student = $stmt->fetch();

        if ($student && password_verify($password, $student['password'])) {
            if (($student['account_status'] ?? 'active') === 'blocked') {
                $error = "This account is blocked. Contact support if you need it reviewed.";
            } elseif ((int)($student['email_verified'] ?? 0) !== 1) {
                if (send_otp_email($pdo, $email, 'registration', (int)$student['id'])) {
                    $_SESSION['pending_verify_email'] = $email;
                    redirect(app_path('otp?purpose=registration&email=' . rawurlencode($email)));
                }
                $error = "Email is not verified, and OTP could not be sent right now. Please try again.";
            } else {
                session_regenerate_id(true);
                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_name'] = $student['name'];
                header('Location: ' . localized_url(''));
                exit;
            }
        } else {
            $error = "Invalid email or password.";
        }
        }
    } 
    elseif ($action == 'register') {
        $name = trim($_POST['name'] ?? '');
        if (!empty($name) && $email && password_meets_policy($password)) {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id, email_verified FROM students WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $existing = $stmt->fetch();
            if ($existing && (int)($existing['email_verified'] ?? 0) === 1) {
                $error = "An account with this email already exists.";
            } elseif ($existing) {
                $studentId = (int)$existing['id'];
                if (send_otp_email($pdo, $email, 'registration', $studentId)) {
                    $_SESSION['pending_verify_email'] = $email;
                    redirect(app_path('otp?purpose=registration&email=' . rawurlencode($email)));
                }
                $error = "Account created, but OTP email could not be sent. Please try again.";
            } else {
                $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO students (name, email, password, email_verified, profile_public) VALUES (:name, :email, :password, 0, 0)");
                $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashed_pw]);
                $studentId = (int)$pdo->lastInsertId();
                if (send_otp_email($pdo, $email, 'registration', $studentId)) {
                    $_SESSION['pending_verify_email'] = $email;
                    redirect(app_path('otp?purpose=registration&email=' . rawurlencode($email)));
                }
                $error = "Account created, but OTP email could not be sent. Please try again.";
            }
        } else {
            $error = "Use your name, a valid email, and a password with at least 8 characters, including a letter and a number.";
        }
    }
}

$seo_title = "Student Login";
$seo_robots = 'noindex, nofollow';
$seo_description = "Log in to track your programming courses, lessons, and learning progress.";
require_once 'includes/header.php';
?>

<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-slate-50">
    <!-- Subtle background decoration -->

    <div class="max-w-4xl w-full grid grid-cols-1 lg:grid-cols-12 bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden relative z-10">
        
        <!-- Left Branding Panel (Desktop Only) -->
        <div class="lg:col-span-5 auth-intro p-8 sm:p-10 text-white flex flex-col justify-between relative overflow-hidden hidden lg:flex">
            
            <div class="relative z-10">
                <span class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Student Portal
                </span>
                <h3 class="text-2xl font-black tracking-tight text-white mb-4 leading-tight">
                    Start Your Developer Journey Today
                </h3>
                <p class="text-slate-300 text-sm leading-relaxed mb-8">
                    Create a free student profile to record your lesson progress, pass chapter quizzes, and generate your shareable digital certificate.
                </p>

                <div class="space-y-3.5 text-sm text-slate-200">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-black">&check;</span>
                        <span>Track progress across 6 full languages</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-black">&check;</span>
                        <span>Interactive chapter quizzes &amp; scores</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-black">&check;</span>
                        <span>Shareable student QR profile card</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-black">&check;</span>
                        <span>100% free learning forever</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 pt-8 border-t border-slate-800 text-xs text-slate-400">
                Over 10,000+ beginners learning with Learn.Nectra
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="lg:col-span-7 p-8 sm:p-10 flex flex-col justify-center">
            <div class="mb-6">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded inline-block mb-3">
                    <?php echo h(t('login.kicker')); ?>
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight"><?php echo h(t('login.welcome')); ?></h2>
                <p class="mt-1 text-sm text-slate-500"><?php echo h(t('login.lead')); ?></p>
            </div>

            <?php if($error): ?>
                <div class="bg-red-50 text-red-700 p-3.5 rounded-xl text-sm font-semibold border border-red-200 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span><?php echo h($error); ?></span>
                </div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="bg-emerald-50 text-emerald-700 p-3.5 rounded-xl text-sm font-semibold border border-emerald-200 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span><?php echo h($success); ?></span>
                </div>
            <?php endif; ?>

            <?php if($google_enabled): ?>
            <div class="mb-5">
                <a href="<?php echo h($google_login_url); ?>" class="w-full flex items-center justify-center px-4 py-3 border border-slate-300 rounded-xl shadow-sm bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 gap-3">
                    <svg class="w-5 h-5" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.73 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                    <span><?php echo h(t('login.google')); ?></span>
                </a>
            </div>

            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                <div class="relative flex justify-center text-xs uppercase font-bold text-slate-400"><span class="px-3 bg-white"><?php echo h(t('login.or_email')); ?></span></div>
            </div>
            <?php endif; ?>

            <div x-data="{ tab: 'login', showPw: false }">
                <div class="flex border-b border-slate-200 mb-6 bg-slate-50 p-1 rounded-xl">
                    <button type="button" @click="tab = 'login'" :class="tab === 'login' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'" class="w-1/2 py-2 text-center rounded-lg text-sm transition-all"><?php echo h(t('login.sign_in')); ?></button>
                    <button type="button" @click="tab = 'register'" :class="tab === 'register' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'" class="w-1/2 py-2 text-center rounded-lg text-sm transition-all"><?php echo h(t('login.create')); ?></button>
                </div>

                <!-- Sign In Form -->
                <form x-show="tab === 'login'" method="POST" action="<?php echo h(lang_href('login')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5"><?php echo h(t('login.email')); ?></label>
                        <input type="email" name="email" required autocomplete="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white" placeholder="you@example.com">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700"><?php echo h(t('login.password')); ?></label>
                            <a href="<?php echo h(lang_href('otp?purpose=password_reset')); ?>" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline"><?php echo h(t('login.forgot')); ?></a>
                        </div>
                        <div class="relative">
                            <input :type="showPw ? 'text' : 'password'" name="password" required autocomplete="current-password" class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white" placeholder="<?php echo h(t('login.password_ph')); ?>">
                            <button type="button" @click="showPw = !showPw" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold">
                                <span x-text="showPw ? <?php echo h(json_encode(t('login.hide'), JSON_UNESCAPED_UNICODE)); ?> : <?php echo h(json_encode(t('login.show'), JSON_UNESCAPED_UNICODE)); ?>"></span>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="w-full edu-primary-btn py-3 px-4 font-bold text-sm shadow-md transition-all hover:shadow-lg mt-2">
                        <?php echo h(t('login.sign_in')); ?> &raquo;
                    </button>
                </form>

                <!-- Register Form -->
                <form x-show="tab === 'register'" method="POST" action="<?php echo h(lang_href('login')); ?>" class="space-y-4" style="display: none;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="register">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5"><?php echo h(t('login.name')); ?></label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white" placeholder="Alex Morgan">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5"><?php echo h(t('login.email')); ?></label>
                        <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white" placeholder="alex@example.com">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5"><?php echo h(t('login.create_password')); ?></label>
                        <div class="relative">
                            <input :type="showPw ? 'text' : 'password'" name="password" minlength="8" required class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white" placeholder="<?php echo h(t('login.password_new_ph')); ?>">
                            <button type="button" @click="showPw = !showPw" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold">
                                <span x-text="showPw ? <?php echo h(json_encode(t('login.hide'), JSON_UNESCAPED_UNICODE)); ?> : <?php echo h(json_encode(t('login.show'), JSON_UNESCAPED_UNICODE)); ?>"></span>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1"><?php echo h(t('login.password_hint')); ?></p>
                    </div>
                    <button type="submit" class="w-full edu-primary-btn py-3 px-4 font-bold text-sm shadow-md transition-all hover:shadow-lg mt-2">
                        <?php echo h(t('login.create_submit')); ?> &raquo;
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<?php require_once 'includes/footer.php'; ?>
