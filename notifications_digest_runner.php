<?php
require_once 'includes/db.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$expectedToken = getenv('DIGEST_RUNNER_TOKEN') ?: '';
if ($expectedToken === '' || !hash_equals($expectedToken, (string)$token)) {
    http_response_code(403);
    exit('Unauthorized');
}

$result = run_due_notification_digests($pdo, 200);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['status' => 'ok', 'result' => $result], JSON_UNESCAPED_SLASHES);
?>
