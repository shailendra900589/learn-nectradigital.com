<?php
declare(strict_types=1);

function razorpay_key_id(): string
{
    return app_secret('RAZORPAY_KEY_ID');
}

function razorpay_enabled(): bool
{
    return razorpay_key_id() !== '' && app_secret('RAZORPAY_KEY_SECRET') !== '' && function_exists('curl_init');
}

function razorpay_create_order(int $amountPaise, string $receipt, array $notes): ?array
{
    if (!razorpay_enabled()) {
        return null;
    }
    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERPWD => razorpay_key_id() . ':' . app_secret('RAZORPAY_KEY_SECRET'),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode([
            'amount' => $amountPaise,
            'currency' => 'INR',
            'receipt' => substr($receipt, 0, 40),
            'notes' => $notes,
        ]),
    ]);
    $body = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $order = is_string($body) ? json_decode($body, true) : null;
    if ($status !== 200 || !is_array($order) || empty($order['id'])) {
        error_log('Razorpay order failed with HTTP ' . $status);
        return null;
    }
    return $order;
}

function razorpay_captured_payment_id(string $orderId): ?string
{
    if (!razorpay_enabled() || !preg_match('/^order_[A-Za-z0-9]+$/', $orderId)) {
        return null;
    }
    $ch = curl_init('https://api.razorpay.com/v1/orders/' . $orderId . '/payments');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERPWD => razorpay_key_id() . ':' . app_secret('RAZORPAY_KEY_SECRET'),
    ]);
    $body = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = is_string($body) ? json_decode($body, true) : null;
    if ($status !== 200 || !is_array($result)) {
        return null;
    }
    foreach ((array)($result['items'] ?? []) as $payment) {
        if (($payment['status'] ?? '') === 'captured' && !empty($payment['id'])) {
            return (string)$payment['id'];
        }
    }
    return null;
}

function razorpay_payment_signature_valid(string $orderId, string $paymentId, string $signature): bool
{
    $secret = app_secret('RAZORPAY_KEY_SECRET');
    if ($secret === '' || $orderId === '' || $paymentId === '' || $signature === '') {
        return false;
    }
    return hash_equals(hash_hmac('sha256', $orderId . '|' . $paymentId, $secret), $signature);
}

function razorpay_webhook_signature_valid(string $body, string $signature): bool
{
    $secret = app_secret('RAZORPAY_WEBHOOK_SECRET');
    if ($secret === '' || $signature === '') {
        return false;
    }
    return hash_equals(hash_hmac('sha256', $body, $secret), $signature);
}
