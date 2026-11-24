<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit;
}

$body = (string)file_get_contents('php://input', false, null, 0, 1048576);
$signature = (string)($_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '');
if (!razorpay_webhook_signature_valid($body, $signature)) {
    http_response_code(401);
    exit;
}

$event = json_decode($body, true);
$type = is_array($event) ? (string)($event['event'] ?? '') : '';
if (in_array($type, ['payment.captured', 'order.paid'], true)) {
    $payment = $event['payload']['payment']['entity'] ?? [];
    $orderId = (string)($payment['order_id'] ?? ($event['payload']['order']['entity']['id'] ?? ''));
    $paymentId = (string)($payment['id'] ?? '');
    $stmt = $pdo->prepare("SELECT id FROM payments WHERE gateway_order_id = :order_id LIMIT 1");
    $stmt->execute(['order_id' => substr($orderId, 0, 64)]);
    $known = $orderId !== '' && $stmt->fetchColumn();
    if ($known && $paymentId !== '' && !complete_gateway_payment($pdo, substr($orderId, 0, 64), substr($paymentId, 0, 64))) {
        http_response_code(500);
        exit;
    }
}

http_response_code(200);
header('Content-Type: application/json');
echo '{"ok":true}';
