<?php
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/content.php';
require_once __DIR__ . '/profile_helpers.php';
require_once __DIR__ . '/subscriptions.php';
require_once __DIR__ . '/notifications.php';
require_once __DIR__ . '/recommendations.php';
require_once __DIR__ . '/editorial_workflow.php';
require_once __DIR__ . '/analytics.php';

// Database configuration. Environment variables are preferred for production.
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'learn_lms';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Database connection failed. Please try again later.');
}

ensure_subscription_schema($pdo);
ensure_notification_schema($pdo);
ensure_editorial_workflow_schema($pdo);
ensure_analytics_schema($pdo);
track_lifetime_visitor($pdo);
?>
