<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

function send_security_headers(): void
{
    if (headers_sent() || PHP_SAPI === 'cli') {
        return;
    }
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(self "https://api.razorpay.com"), usb=(), bluetooth=(), interest-cohort=()');
    header('X-Permitted-Cross-Domain-Policies: none');
    header("Content-Security-Policy: base-uri 'self'; object-src 'none'; frame-ancestors 'none'; form-action 'self'");
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function request_cookie_path(): string
{
    $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    if (preg_match('#/admin$#', $dir)) {
        $dir = rtrim(dirname($dir), '/');
    }
    if ($dir === '' || $dir === '.' || $dir === '/') {
        return '/';
    }
    return $dir . '/';
}

function app_secret(string $key): string
{
    $env = getenv($key);
    if (is_string($env) && $env !== '') {
        return $env;
    }

    static $values = null;
    if ($values === null) {
        $values = [];
        $appRoot = dirname(__DIR__);
        $outside = dirname($appRoot);
        if (PHP_OS_FAMILY === 'Windows' && realpath($outside) === realpath('C:/xampp/htdocs')) {
            $outside = dirname($appRoot, 2);
        }
        $path = $outside . '/nectra-oauth.local.php';
        if (is_file($path)) {
            $loaded = require $path;
            if (is_array($loaded)) {
                $values = $loaded;
            }
        }
    }
    return is_string($values[$key] ?? null) ? $values[$key] : '';
}

function password_meets_policy(string $password): bool
{
    return strlen($password) >= 8
        && preg_match('/[A-Za-z]/', $password) === 1
        && preg_match('/\d/', $password) === 1;
}

function like_contains(string $value): string
{
    return '%' . addcslashes($value, '\\%_') . '%';
}

function expire_idle_login(): void
{
    $now = time();
    $last = (int)($_SESSION['last_activity'] ?? 0);
    if ($last > 0 && ($now - $last) > 43200) {
        unset(
            $_SESSION['student_logged_in'],
            $_SESSION['student_id'],
            $_SESSION['student_name'],
            $_SESSION['admin_logged_in'],
            $_SESSION['admin_id'],
            $_SESSION['admin_username']
        );
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
    }
    $_SESSION['last_activity'] = $now;
}

function secure_session_start(): void
{
    send_security_headers();

    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['asset_session_ok'] = 1;
        expire_idle_login();
        return;
    }

    $localSessionPath = __DIR__ . '/../sessions';
    if (!is_dir($localSessionPath)) {
        @mkdir($localSessionPath, 0700, true);
    }
    if (is_dir($localSessionPath)) {
        session_save_path($localSessionPath);
    }

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    ini_set('session.gc_probability', '0');
    ini_set('session.gc_maxlifetime', '43200');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => request_cookie_path(),
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    $_SESSION['asset_session_ok'] = 1;
    expire_idle_login();
    register_shutdown_function('cleanup_runtime_files');
}

function cleanup_runtime_files(): void
{
    $root = __DIR__ . '/../sessions';
    $marker = $root . '/.cleanup-at';
    $now = time();
    if (!is_dir($root) || (is_file($marker) && $now - (int)@filemtime($marker) < 1800)) {
        return;
    }
    $lock = @fopen($marker, 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
        if ($lock) fclose($lock);
        return;
    }

    try {
        @touch($marker, $now);
        $current = session_status() === PHP_SESSION_ACTIVE ? 'sess_' . session_id() : '';
        $checked = 0;
        foreach (new DirectoryIterator($root) as $file) {
            if ($checked >= 5000) {
                break;
            }
            $name = $file->getFilename();
            if (!$file->isFile() || !str_starts_with($name, 'sess_') || $name === $current) {
                continue;
            }
            $checked++;
            $age = $now - $file->getMTime();
            if ($age > 43200) {
                @unlink($file->getPathname());
            } elseif ($age > 10800) {
                $data = (string)@file_get_contents($file->getPathname(), false, null, 0, 8192);
                if (!str_contains($data, 'student_logged_in') && !str_contains($data, 'admin_logged_in')) {
                    @unlink($file->getPathname());
                }
            }
        }

        foreach ((glob($root . '/request-budgets/*.json') ?: []) as $path) {
            if ($now - (int)@filemtime($path) > 86400) {
                @unlink($path);
            }
        }
        $today = gmdate('Ymd');
        foreach ((glob($root . '/course-rank-caps/*.json') ?: []) as $path) {
            if (!str_starts_with(basename($path), $today)) {
                @unlink($path);
            }
        }
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

function enforce_request_budget(string $scope, int $maximum, int $seconds): void
{
    if (PHP_SAPI === 'cli') {
        return;
    }

    $address = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $packedAddress = @inet_pton($address);
    $key = hash('sha256', $scope . '|' . ($packedAddress ?: $address));
    $directory = __DIR__ . '/../sessions/request-budgets';
    if (!is_dir($directory)) {
        @mkdir($directory, 0700, true);
    }
    $handle = @fopen($directory . '/' . substr($key, 0, 2) . '.json', 'c+');
    if (!$handle || !flock($handle, LOCK_EX)) {
        if ($handle) fclose($handle);
        error_log('Could not open the request budget storage.');
        http_response_code(503);
        exit('Temporarily unavailable. Please retry shortly.');
    }

    try {
        $entries = json_decode(stream_get_contents($handle), true) ?: [];
        $now = time();
        $entries = array_filter($entries, static fn(array $row): bool => $row[0] > $now);
        [$expires, $count] = $entries[$key] ?? [$now + $seconds, 0];
        $allowed = $count < $maximum;
        if ($allowed) {
            $entries[$key] = [$expires, $count + 1];
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($entries, JSON_THROW_ON_ERROR));
            fflush($handle);
        }
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    if (!$allowed) {
        header('Retry-After: ' . max(1, $expires - time()));
        header('Cache-Control: no-store');
        http_response_code(429);
        exit('Too many requests. Please retry shortly.');
    }
}

function h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function require_admin(): void
{
    secure_session_start();
    if (empty($_SESSION['admin_logged_in'])) {
        redirect('login');
    }
}

function csrf_token(): string
{
    secure_session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf(): void
{
    secure_session_start();
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (!is_string($token) || $token === '' || !is_string($sessionToken) || $sessionToken === '' || !hash_equals($sessionToken, $token)) {
        http_response_code(403);
        exit('Invalid security token. Please refresh the page and try again.');
    }
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

function post_int(string $key, int $default = 0): int
{
    return filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT) ?: $default;
}

function get_int(string $key, int $default = 0): int
{
    return filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT) ?: $default;
}

function asset_signing_secret(): string
{
    static $secret = null;
    if ($secret === null) {
        $envKey = getenv('APP_KEY');
        if ($envKey !== false && $envKey !== '') {
            $secret = hash('sha256', $envKey);
            return $secret;
        }

        $directory = __DIR__ . '/../sessions';
        if (!is_dir($directory)) {
            @mkdir($directory, 0700, true);
        }
        $keyFile = $directory . '/application.key';
        $handle = @fopen($keyFile, 'c+');
        if (!$handle || !flock($handle, LOCK_EX)) {
            if ($handle) fclose($handle);
            throw new RuntimeException('Application signing key is unavailable.');
        }
        try {
            $secret = trim(stream_get_contents($handle));
            if (!preg_match('/^[a-f0-9]{64}$/D', $secret)) {
                $secret = bin2hex(random_bytes(32));
                rewind($handle);
                if (!ftruncate($handle, 0) || fwrite($handle, $secret) !== 64 || !fflush($handle)) {
                    throw new RuntimeException('Could not persist the application signing key.');
                }
                @chmod($keyFile, 0600);
            }
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
    return $secret;
}

function protected_asset_url(string $relativePath): string
{
    $cleanPath = ltrim(str_replace('\\', '/', trim($relativePath)), '/');
    $fullPath = dirname(__DIR__) . '/' . $cleanPath;
    $mtime = is_file($fullPath) ? (int)filemtime($fullPath) : 1;
    $window = intdiv(time(), 3600);
    $sig = substr(hash_hmac('sha256', $cleanPath . '|' . $mtime . '|' . $window, asset_signing_secret()), 0, 20);
    return $cleanPath . '?v=' . $mtime . '&_s=' . $sig;
}

function verify_asset_signature(string $relativePath, int $mtime, string $sig): bool
{
    if (strlen($sig) !== 20 || !ctype_xdigit($sig)) {
        return false;
    }
    $cleanPath = ltrim(str_replace('\\', '/', trim($relativePath)), '/');
    $currentWindow = intdiv(time(), 3600);
    foreach ([$currentWindow, $currentWindow - 1] as $win) {
        $expected = substr(hash_hmac('sha256', $cleanPath . '|' . $mtime . '|' . $win, asset_signing_secret()), 0, 20);
        if (hash_equals($expected, strtolower($sig))) {
            return true;
        }
    }
    return false;
}
?>
