<?php
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/content.php';
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/translate.php';
require_once __DIR__ . '/profile_helpers.php';
require_once __DIR__ . '/subscriptions.php';
require_once __DIR__ . '/course_rank.php';

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/

$localConfig = [];
$configDir = dirname(__DIR__, 2);
if (PHP_OS_FAMILY === 'Windows' && realpath($configDir) === realpath('C:/xampp/htdocs')) {
    $configDir = dirname(__DIR__, 3);
}
$configPath = $configDir . '/nectra-learning.local.php';
if (is_file($configPath)) {
    $loadedConfig = require $configPath;
    if (is_array($loadedConfig)) {
        $localConfig = $loadedConfig;
    }
}
$host = getenv('DB_HOST') ?: ($localConfig['DB_HOST'] ?? 'localhost');
$dbname = getenv('DB_NAME') ?: ($localConfig['DB_NAME'] ?? '');
$username = getenv('DB_USER') ?: ($localConfig['DB_USER'] ?? '');
$password = getenv('DB_PASS');
if ($password === false) {
    $password = $localConfig['DB_PASS'] ?? '';
}

/*
|--------------------------------------------------------------------------
| PDO Connection
|--------------------------------------------------------------------------
*/

try {

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 5
    ]);

} catch (PDOException $e) {

    error_log('Database connection failed: ' . $e->getMessage());

    http_response_code(503);
    exit('The learning platform is temporarily unavailable.');
}

/*
|--------------------------------------------------------------------------
| Ensure Schema
|--------------------------------------------------------------------------
*/

ensure_subscription_schema($pdo);
ensure_course_rank_schema($pdo);
?>