<?php
require_once 'includes/db.php';
require_once 'includes/mailer.php';
secure_session_start();

// If student is already logged in, send them to the homepage
if (isset($_SESSION['student_logged_in'])) {
    header('Location: ' . app_path('index'));
    exit;
}

$error = '';
$success = '';

// ==========================================
// 1. GOOGLE OAUTH 2.0 CONFIGURATION
// ==========================================
$google_client_id = getenv('GOOGLE_CLIENT_ID') ?: '';
$google_client_secret = getenv('GOOGLE_CLIENT_SECRET') ?: '';
$google_redirect_uri = getenv('GOOGLE_REDIRECT_URI') ?: 'https://learn.nectradigital.com/login';
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
        'state' => $_SESSION['google_oauth_state'],
        'prompt' => 'select_account',
    ]);
}

// ==========================================
// 2. HANDLE GOOGLE CALLBACK (User returns from Google)
// ==========================================
if ($google_enabled && isset($_GET['code'])) {
    if (empty($_GET['state']) || !hash_equals($_SESSION['google_oauth_state'] ?? '', (string)$_GET['state'])) {
        $error = "Google Login Failed. Please refresh and try again.";
    } else {
    // Exchange the authorization code for an access token
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token_response['access_token']]);
        $google_profile = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($google_profile['email'])) {
            $email = filter_var($google_profile['email'], FILTER_VALIDATE_EMAIL);
            $name = trim($google_profile['name'] ?? 'Student');

            if (!$email) {
                $error = "Google Login Failed. Your Google account did not return a valid email.";
            } else {

            // Check if student already exists
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $student = $stmt->fetch();

            if ($student) {
                // Log them in
                $pdo->prepare("UPDATE students SET email_verified = 1, email_verified_at = COALESCE(email_verified_at, NOW()) WHERE id = :id")->execute(['id' => $student['id']]);
                session_regenerate_id(true);
                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_name'] = $student['name'];
                create_notification(
                    $pdo,
                    (int)$student['id'],
                    'New sign in detected',
                    'Your account was signed in using Google.',
                    'security',
                    site_base_url() . 'student'
                );
            } else {
                // Create new account for Google User (Generate random password since they use Google)
                $random_password = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO students (name, email, password, email_verified, email_verified_at) VALUES (:name, :email, :password, 1, NOW())");
                $stmt->execute(['name' => $name, 'email' => $email, 'password' => $random_password]);
                $newStudentId = (int)$pdo->lastInsertId();
                session_regenerate_id(true);
                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_id'] = $newStudentId;
                $_SESSION['student_name'] = $name;
                create_notification(
                    $pdo,
                    $newStudentId,
                    'Welcome to Learn.Nectra',
                    'Your Google account has been connected successfully. Start with your first course now.',
                    'system',
                    site_base_url() . 'index#courses'
                );
            }
            header('Location: ' . app_path('index'));
            exit;
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
            if ((int)($student['email_verified'] ?? 0) !== 1) {
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
                create_notification(
                    $pdo,
                    (int)$student['id'],
                    'New sign in detected',
                    'Your account was signed in with email and password.',
                    'security',
                    site_base_url() . 'student'
                );
                header('Location: ' . app_path('index'));
                exit;
            }
        } else {
            $error = "Invalid email or password.";
        }
        }
    } 
    elseif ($action == 'register') {
        $name = trim($_POST['name'] ?? '');
        if (!empty($name) && $email && strlen($password) >= 8) {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id, email_verified FROM students WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $existing = $stmt->fetch();
            if ($existing && (int)($existing['email_verified'] ?? 0) === 1) {
                $error = "An account with this email already exists.";
            } else {
                $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
                if ($existing) {
                    $stmt = $pdo->prepare("UPDATE students SET name = :name, password = :password WHERE id = :id");
                    $stmt->execute(['name' => $name, 'password' => $hashed_pw, 'id' => $existing['id']]);
                    $studentId = (int)$existing['id'];
                } else {
                    $stmt = $pdo->prepare("INSERT INTO students (name, email, password, email_verified) VALUES (:name, :email, :password, 0)");
                    $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashed_pw]);
                    $studentId = (int)$pdo->lastInsertId();
                }
                if (send_otp_email($pdo, $email, 'registration', $studentId)) {
                    $_SESSION['pending_verify_email'] = $email;
                    redirect(app_path('otp?purpose=registration&email=' . rawurlencode($email)));
                }
                $error = "Account created, but OTP email could not be sent. Please try again.";
            }
        } else {
            $error = "Use your name, a valid email, and a password with at least 8 characters.";
        }
    }
}

$seo_title = "Student Login";
$seo_description = "Log in to track your programming courses, lessons, and learning progress.";
require_once 'includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 site-hero-light">
    <div class="max-w-md w-full space-y-8 edu-soft-card p-8 sm:p-10 rounded-2xl">
        
        <div class="text-center">
            <span class="edu-pill mb-4">Student Access</span>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-950">Welcome Back</h2>
            <p class="mt-2 text-sm text-slate-600">Sign in to track your learning progress.</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm font-medium border border-red-100 text-center"><?php echo h($error); ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="bg-green-50 text-green-600 p-3 rounded-lg text-sm font-medium border border-green-100 text-center"><?php echo h($success); ?></div>
        <?php endif; ?>

        <?php if($google_enabled): ?>
        <div>
            <a href="<?php echo h($google_login_url); ?>" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-3" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.73 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                Sign in with Google
            </a>
        </div>
        <?php endif; ?>

        <?php if($google_enabled): ?>
        <div class="relative">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
            <div class="relative flex justify-center text-sm"><span class="px-2 bg-white text-gray-500">Or continue with email</span></div>
        </div>
        <?php endif; ?>

        <div x-data="{ tab: 'login' }" class="mt-6">
            <div class="flex border-b border-gray-200 mb-6">
                <button @click="tab = 'login'" :class="tab === 'login' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/2 py-2 text-center border-b-2 font-medium text-sm transition">Login</button>
                <button @click="tab = 'register'" :class="tab === 'register' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/2 py-2 text-center border-b-2 font-medium text-sm transition">Register</button>
            </div>

            <form x-show="tab === 'login'" method="POST" action="login" class="space-y-5">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="login">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email address</label>
                    <input type="email" name="email" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <div class="text-right mt-2">
                        <a href="otp?purpose=password_reset" class="text-sm font-bold text-blue-600 hover:underline">Forgot password?</a>
                    </div>
                </div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 edu-primary-btn text-sm transition">
                    Sign In
                </button>
            </form>

            <form x-show="tab === 'register'" method="POST" action="login" class="space-y-5" style="display: none;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="register">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email address</label>
                    <input type="email" name="email" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" minlength="8" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 edu-primary-btn text-sm transition">
                    Create Account
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<?php require_once 'includes/footer.php'; ?>
