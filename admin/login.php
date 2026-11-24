<?php
require_once '../includes/db.php';
require_once 'ui.php';
secure_session_start();

$error = '';
$success = '';
$admin_count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();
    enforce_request_budget('admin-login', 12, 900);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($admin_count === 0 && (getenv('ALLOW_ADMIN_BOOTSTRAP') !== '1' || !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true))) {
        $error = 'Administrator setup is unavailable.';
    } elseif ($admin_count === 0) {
        if (preg_match('/^[a-zA-Z0-9_.-]{3,40}$/', $username) && strlen($password) >= 10) {
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->execute([
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $success = "Administrator account created. Sign in with the credentials you just set.";
            $admin_count = 1;
        } else {
            $error = "Choose a username with 3-40 safe characters and a password with at least 10 characters.";
        }
    } elseif (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            // Redirect to dashboard
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign In | Learn.Nectra</title>
    <?php echo admin_styles(); ?>
</head>
<body class="admin-login">
    <main class="admin-login-layout">
        <section class="admin-login-intro">
            <a href="../index" class="admin-brand">
                <span class="admin-brand-mark" aria-hidden="true">N</span>
                <span>Learn.Nectra<small>Administration</small></span>
            </a>
            <div>
                <p class="admin-login-kicker">Learning platform</p>
                <p class="admin-login-display">Manage your learning platform.</p>
                <p>Publish courses, manage student access, and review payments in one place.</p>
            </div>
        </section>
        <section class="admin-login-form" aria-labelledby="admin-login-title">
            <h1 id="admin-login-title"><?php echo $admin_count === 0 ? 'Create an admin account' : 'Sign in'; ?></h1>
            <p><?php echo $admin_count === 0 ? 'Set up the first administrator account.' : 'Use your administrator credentials to continue.'; ?></p>
            <?php if ($error): ?>
                <div class="admin-login-alert admin-login-error" role="alert"><?php echo h($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="admin-login-alert admin-login-success" role="status"><?php echo h($success); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <?php echo csrf_field(); ?>
                <label for="admin-username">Username</label>
                <input id="admin-username" name="username" class="admin-input" autocomplete="username" required>
                <label for="admin-password">Password</label>
                <input id="admin-password" type="password" name="password" class="admin-input" autocomplete="<?php echo $admin_count === 0 ? 'new-password' : 'current-password'; ?>" required>
                <button type="submit" class="admin-btn-primary"><?php echo $admin_count === 0 ? 'Create account' : 'Sign in'; ?></button>
            </form>
            <a class="admin-return-link" href="../index">Return to Learn.Nectra</a>
        </section>
    </main>
</body>
</html>
