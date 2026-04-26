<?php
require_once 'includes/db.php';
require_once 'includes/mailer.php';
secure_session_start();

if (!empty($_SESSION['student_logged_in'])) {
    redirect(app_path('student'));
}

$purpose = ($_GET['purpose'] ?? $_POST['purpose'] ?? 'registration') === 'password_reset' ? 'password_reset' : 'registration';
$email = filter_var(trim($_GET['email'] ?? $_POST['email'] ?? ($_SESSION['pending_verify_email'] ?? '')), FILTER_VALIDATE_EMAIL) ?: '';
$step = $purpose === 'password_reset' && $email === '' ? 'request' : 'verify';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: $email;

    if ($action === 'request_reset') {
        if (!$email) {
            $error = 'Enter a valid email address.';
            $step = 'request';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $studentId = (int)($stmt->fetchColumn() ?: 0);
            if ($studentId > 0) {
                send_otp_email($pdo, $email, 'password_reset', $studentId);
            }
            $_SESSION['pending_reset_email'] = $email;
            $success = 'If this email exists, a password reset OTP has been sent.';
            $purpose = 'password_reset';
            $step = 'verify';
        }
    } elseif ($action === 'resend') {
        if (!$email) {
            $error = 'Email is required to resend OTP.';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $studentId = (int)($stmt->fetchColumn() ?: 0);
            if ($studentId > 0 && send_otp_email($pdo, $email, $purpose, $studentId)) {
                $success = 'A fresh OTP has been sent.';
            } else {
                $error = 'Could not send OTP. Please try again.';
            }
        }
    } elseif ($action === 'verify_registration') {
        $otp = preg_replace('/\D+/', '', $_POST['otp'] ?? '');
        $result = verify_otp($pdo, $email, 'registration', $otp);
        if (!$email || strlen($otp) !== 6) {
            $error = 'Enter the 6-digit OTP.';
        } elseif (!$result['ok']) {
            $error = $result['message'];
        } else {
            $stmt = $pdo->prepare("UPDATE students SET email_verified = 1, email_verified_at = NOW() WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $stmtStudent = $pdo->prepare("SELECT id, name FROM students WHERE email = :email LIMIT 1");
            $stmtStudent->execute(['email' => $email]);
            $student = $stmtStudent->fetch();
            if ($student) {
                session_regenerate_id(true);
                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_name'] = $student['name'];
                unset($_SESSION['pending_verify_email']);
                redirect(app_path('student'));
            }
            $error = 'Account verified, but login failed. Please sign in.';
        }
    } elseif ($action === 'reset_password') {
        $otp = preg_replace('/\D+/', '', $_POST['otp'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        if (!$email || strlen($otp) !== 6) {
            $error = 'Enter the 6-digit OTP.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'New password must be at least 8 characters.';
        } else {
            $result = verify_otp($pdo, $email, 'password_reset', $otp);
            if (!$result['ok']) {
                $error = $result['message'];
            } else {
                $stmt = $pdo->prepare("UPDATE students SET password = :password, email_verified = 1, email_verified_at = COALESCE(email_verified_at, NOW()) WHERE email = :email");
                $stmt->execute(['password' => password_hash($newPassword, PASSWORD_DEFAULT), 'email' => $email]);
                unset($_SESSION['pending_reset_email']);
                $success = 'Password updated. You can now log in.';
                $step = 'done';
            }
        }
    }
}

$seo_title = $purpose === 'password_reset' ? 'Reset Password' : 'Verify Email';
$seo_description = 'Verify your Learn.Nectra account using a secure email OTP.';
$seo_robots = 'noindex, nofollow';
require_once 'includes/header.php';
?>

<section class="min-h-[80vh] flex items-center justify-center px-4 py-12 site-hero-light">
    <div class="w-full max-w-md edu-soft-card rounded-2xl p-8">
        <div class="text-center mb-6">
            <span class="edu-pill mb-4"><?php echo $purpose === 'password_reset' ? 'Secure Reset' : 'Email OTP'; ?></span>
            <h1 class="text-3xl font-black text-slate-900"><?php echo $purpose === 'password_reset' ? 'Reset Password' : 'Verify Your Email'; ?></h1>
            <p class="text-sm text-slate-500 mt-2"><?php echo $purpose === 'password_reset' ? 'Use OTP to create a new password.' : 'Enter the OTP sent to your email to activate your account.'; ?></p>
        </div>

        <?php if($error): ?><div class="mb-4 bg-red-50 border border-red-100 text-red-700 rounded-lg p-3 text-sm font-bold"><?php echo h($error); ?></div><?php endif; ?>
        <?php if($success): ?><div class="mb-4 bg-green-50 border border-green-100 text-green-700 rounded-lg p-3 text-sm font-bold"><?php echo h($success); ?></div><?php endif; ?>
        <?php if(is_local_request() && !empty($_SESSION['dev_last_otp']) && ($_SESSION['dev_last_otp_email'] ?? '') === $email && ($_SESSION['dev_last_otp_purpose'] ?? '') === $purpose): ?>
            <div class="mb-4 bg-amber-50 border border-amber-100 text-amber-800 rounded-lg p-3 text-sm font-bold">
                Local test OTP: <span class="text-xl tracking-widest"><?php echo h($_SESSION['dev_last_otp']); ?></span>
            </div>
        <?php endif; ?>

        <?php if($step === 'request'): ?>
            <form method="POST" action="otp" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="purpose" value="password_reset">
                <input type="hidden" name="action" value="request_reset">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Email address</label>
                    <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <button class="w-full edu-primary-btn py-3">Send OTP</button>
            </form>
        <?php elseif($step === 'done'): ?>
            <a href="login" class="block text-center edu-primary-btn py-3">Go to Login</a>
        <?php elseif($purpose === 'password_reset'): ?>
            <form method="POST" action="otp" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="purpose" value="password_reset">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="email" value="<?php echo h($email); ?>">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" value="<?php echo h($email); ?>" class="w-full border border-gray-200 bg-slate-50 rounded-lg px-4 py-3" disabled>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">OTP</label>
                    <input type="text" name="otp" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-center text-2xl font-black tracking-[0.35em] focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">New Password</label>
                    <input type="password" name="new_password" minlength="8" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <button class="w-full edu-primary-btn py-3">Reset Password</button>
            </form>
        <?php else: ?>
            <form method="POST" action="otp" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="purpose" value="registration">
                <input type="hidden" name="action" value="verify_registration">
                <input type="hidden" name="email" value="<?php echo h($email); ?>">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" value="<?php echo h($email); ?>" class="w-full border border-gray-200 bg-slate-50 rounded-lg px-4 py-3" disabled>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">OTP</label>
                    <input type="text" name="otp" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-center text-2xl font-black tracking-[0.35em] focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <button class="w-full edu-primary-btn py-3">Verify & Continue</button>
            </form>
        <?php endif; ?>

        <?php if($email && $step !== 'request' && $step !== 'done'): ?>
            <form method="POST" action="otp" class="mt-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="purpose" value="<?php echo h($purpose); ?>">
                <input type="hidden" name="action" value="resend">
                <input type="hidden" name="email" value="<?php echo h($email); ?>">
                <button class="w-full border border-slate-200 hover:bg-slate-50 text-slate-700 font-black rounded-lg py-3">Resend OTP</button>
            </form>
        <?php endif; ?>

        <div class="mt-5 text-center">
            <a href="login" class="text-sm font-bold text-blue-600 hover:underline">Back to login</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
