<?php
declare(strict_types=1);

function smtp_config(): array
{
    $fromEmail = getenv('SMTP_FROM_EMAIL') ?: (getenv('SMTP_USER') ?: 'nectradigital3@gmail.com');
    return [
        'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'port' => (int)(getenv('SMTP_PORT') ?: 587),
        'username' => getenv('SMTP_USER') ?: 'nectradigital3@gmail.com',
        'password' => getenv('SMTP_PASS') ?: 'qdlyghucipqmmotp',
        'from_email' => $fromEmail,
        'from_name' => getenv('SMTP_FROM_NAME') ?: 'Nectra Digital',
        'reply_to' => getenv('SMTP_REPLY_TO') ?: $fromEmail,
        'helo_domain' => getenv('SMTP_HELO_DOMAIN') ?: 'nectradigital.com',
    ];
}

function smtp_read($socket): string
{
    $data = '';
    while ($line = fgets($socket, 515)) {
        $data .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }
    return $data;
}

function smtp_command($socket, string $command, array $okCodes): string
{
    fwrite($socket, $command . "\r\n");
    $response = smtp_read($socket);
    $code = (int)substr($response, 0, 3);
    if (!in_array($code, $okCodes, true)) {
        throw new RuntimeException('SMTP command failed: ' . trim($response));
    }
    return $response;
}

function send_smtp_mail(string $to, string $subject, string $html, string $text = ''): bool
{
    $config = smtp_config();
    $socket = @stream_socket_client(
        'tcp://' . $config['host'] . ':' . $config['port'],
        $errno,
        $errstr,
        20,
        STREAM_CLIENT_CONNECT
    );

    if (!$socket) {
        error_log('SMTP connect failed: ' . $errstr);
        return false;
    }

    try {
        stream_set_timeout($socket, 20);
        $greeting = smtp_read($socket);
        if ((int)substr($greeting, 0, 3) !== 220) {
            throw new RuntimeException('SMTP greeting failed: ' . trim($greeting));
        }

        smtp_command($socket, 'EHLO ' . $config['helo_domain'], [250]);
        smtp_command($socket, 'STARTTLS', [220]);
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('SMTP TLS failed.');
        }
        smtp_command($socket, 'EHLO ' . $config['helo_domain'], [250]);
        smtp_command($socket, 'AUTH LOGIN', [334]);
        smtp_command($socket, base64_encode($config['username']), [334]);
        smtp_command($socket, base64_encode($config['password']), [235]);
        smtp_command($socket, 'MAIL FROM:<' . $config['from_email'] . '>', [250]);
        smtp_command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
        smtp_command($socket, 'DATA', [354]);

        $boundary = '=_learn_' . bin2hex(random_bytes(12));
        $headers = [
            'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>',
            'Reply-To: ' . $config['reply_to'],
            'Return-Path: ' . $config['from_email'],
            'To: <' . $to . '>',
            'Subject: ' . (function_exists('mb_encode_mimeheader') ? mb_encode_mimeheader($subject, 'UTF-8') : $subject),
            'Date: ' . date(DATE_RFC2822),
            'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $config['helo_domain'] . '>',
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            'X-Mailer: PHP/' . PHP_VERSION,
        ];
        $text = $text !== '' ? $text : strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html));
        $body = implode("\r\n", $headers) . "\r\n\r\n";
        $body .= '--' . $boundary . "\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n" . $text . "\r\n";
        $body .= '--' . $boundary . "\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n" . $html . "\r\n";
        $body .= '--' . $boundary . "--\r\n";
        fwrite($socket, preg_replace('/^\./m', '..', $body) . "\r\n.\r\n");
        $dataResponse = smtp_read($socket);
        if ((int)substr($dataResponse, 0, 3) !== 250) {
            throw new RuntimeException('SMTP DATA failed: ' . trim($dataResponse));
        }
        smtp_command($socket, 'QUIT', [221]);
        fclose($socket);
        return true;
    } catch (Throwable $e) {
        error_log('SMTP mail failed: ' . $e->getMessage());
        fclose($socket);
        return false;
    }
}

function is_local_request(): bool
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return str_contains($host, 'localhost') || str_contains($host, '127.0.0.1') || str_contains($host, '::1');
}

function create_otp(PDO $pdo, string $email, string $purpose, ?int $studentId = null): string
{
    $otp = (string)random_int(100000, 999999);
    $stmt = $pdo->prepare("UPDATE otp_verifications SET consumed_at = NOW() WHERE email = :email AND purpose = :purpose AND consumed_at IS NULL");
    $stmt->execute(['email' => $email, 'purpose' => $purpose]);

    $stmt = $pdo->prepare("
        INSERT INTO otp_verifications (email, student_id, purpose, otp_hash, expires_at)
        VALUES (:email, :student_id, :purpose, :otp_hash, DATE_ADD(NOW(), INTERVAL 10 MINUTE))
    ");
    $stmt->execute([
        'email' => $email,
        'student_id' => $studentId,
        'purpose' => $purpose,
        'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
    ]);
    return $otp;
}

function send_otp_email(PDO $pdo, string $email, string $purpose, ?int $studentId = null): bool
{
    $otp = create_otp($pdo, $email, $purpose, $studentId);
    $title = 'Your verification code';
    $text = "Your Learn.Nectra verification code is {$otp}.\n\nThis code expires in 10 minutes.\nIf you did not request this code, you can ignore this email.";
    $html = '<p>Your Learn.Nectra verification code is:</p><p style="font-size:28px;font-weight:bold;letter-spacing:6px">' . h($otp) . '</p><p>This code expires in 10 minutes.</p><p>If you did not request this code, you can ignore this email.</p>';
    $sent = send_smtp_mail($email, $title, $html, $text);
    if (!$sent && is_local_request()) {
        secure_session_start();
        $_SESSION['dev_last_otp'] = $otp;
        $_SESSION['dev_last_otp_email'] = $email;
        $_SESSION['dev_last_otp_purpose'] = $purpose;
        return true;
    }
    return $sent;
}

function verify_otp(PDO $pdo, string $email, string $purpose, string $otp): array
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM otp_verifications
        WHERE email = :email
          AND purpose = :purpose
          AND consumed_at IS NULL
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->execute(['email' => $email, 'purpose' => $purpose]);
    $row = $stmt->fetch();
    if (!$row) {
        return ['ok' => false, 'message' => 'OTP not found. Please request a new code.'];
    }
    if (strtotime($row['expires_at']) < time()) {
        return ['ok' => false, 'message' => 'OTP expired. Please request a new code.'];
    }
    if ((int)$row['attempts'] >= 5) {
        return ['ok' => false, 'message' => 'Too many attempts. Please request a new code.'];
    }

    $pdo->prepare("UPDATE otp_verifications SET attempts = attempts + 1 WHERE id = :id")->execute(['id' => $row['id']]);
    if (!password_verify($otp, $row['otp_hash'])) {
        return ['ok' => false, 'message' => 'Invalid OTP. Please check the code.'];
    }

    $pdo->prepare("UPDATE otp_verifications SET consumed_at = NOW() WHERE id = :id")->execute(['id' => $row['id']]);
    return ['ok' => true, 'row' => $row];
}
?>
