<?php
declare(strict_types=1);

function smtp_config(): array
{
    $username = app_secret('SMTP_USER') ?: 'nectradigital3@gmail.com';
    $fromEmail = app_secret('SMTP_FROM_EMAIL') ?: $username;
    return [
        'host' => app_secret('SMTP_HOST') ?: 'smtp.gmail.com',
        'port' => (int)(app_secret('SMTP_PORT') ?: 587),
        'username' => $username,
        'password' => app_secret('SMTP_PASS'),
        'from_email' => $fromEmail,
        'from_name' => app_secret('SMTP_FROM_NAME') ?: 'Learn.Nectra',
        'reply_to' => app_secret('SMTP_REPLY_TO') ?: $fromEmail,
        'helo_domain' => app_secret('SMTP_HELO_DOMAIN') ?: 'nectradigital.com',
    ];
}

function email_layout(string $heading, string $bodyHtml, string $ctaText = '', string $ctaUrl = '', string $footerHtml = ''): string
{
    $site = rtrim(site_base_url(), '/');
    $button = '';
    if ($ctaText !== '' && preg_match('#^https?://#i', $ctaUrl)) {
        $button = '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 8px"><tr><td style="border-radius:8px;background:#04AA6D"><a href="' . h($ctaUrl) . '" style="display:inline-block;padding:13px 26px;font-weight:700;font-size:15px;color:#ffffff;text-decoration:none">' . h($ctaText) . '</a></td></tr></table>';
    }
    return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . h($heading) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f1f5f9;font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#1e293b">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:28px 12px"><tr><td align="center">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e2e8f0">'
        . '<tr><td style="background:#0f172a;padding:22px 32px"><a href="' . h($site) . '" style="color:#ffffff;text-decoration:none;font-size:22px;font-weight:800">Learn<span style="color:#34d399">.Nectra</span></a></td></tr>'
        . '<tr><td style="padding:32px 32px 28px"><h1 style="margin:0 0 16px;font-size:24px;line-height:1.3;color:#0f172a">' . h($heading) . '</h1>'
        . '<div style="font-size:15px;line-height:1.7;color:#334155">' . $bodyHtml . '</div>' . $button . '</td></tr>'
        . '<tr><td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:18px 32px;font-size:12px;line-height:1.6;color:#64748b">'
        . 'Learn.Nectra by Nectra Digital &middot; <a href="' . h($site) . '" style="color:#059669">learning.nectradigital.com</a>'
        . ($footerHtml !== '' ? '<br>' . $footerHtml : '')
        . '</td></tr></table></td></tr></table></body></html>';
}

function email_paragraphs(string $text): string
{
    $html = '';
    foreach (preg_split('/\R{2,}/', trim($text)) as $paragraph) {
        if (trim($paragraph) !== '') {
            $html .= '<p style="margin:0 0 14px">' . nl2br(h(trim($paragraph))) . '</p>';
        }
    }
    return $html;
}

function send_branded_mail(string $to, string $subject, string $heading, string $bodyText, string $ctaText = '', string $ctaUrl = '', string $footerHtml = ''): bool
{
    $html = email_layout($heading, email_paragraphs($bodyText), $ctaText, $ctaUrl, $footerHtml);
    $text = $heading . "\n\n" . trim($bodyText) . ($ctaText !== '' && $ctaUrl !== '' ? "\n\n" . $ctaText . ': ' . $ctaUrl : '');
    return send_smtp_mail($to, $subject, $html, $text);
}

function defer_after_response(callable $task): void
{
    register_shutdown_function(static function () use ($task): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        if (function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        } elseif (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        ignore_user_abort(true);
        try {
            $task();
        } catch (Throwable $e) {
            error_log('Deferred task failed: ' . $e->getMessage());
        }
    });
}

function send_password_changed_mail(string $email): bool
{
    return send_branded_mail(
        $email,
        'Your Learn.Nectra password was changed',
        'Password changed',
        "The password for your Learn.Nectra account was just changed.\n\nIf this was you, no action is needed. If you did not change it, reset your password right away and contact support.",
        'Sign in',
        rtrim(site_base_url(), '/') . '/login'
    );
}

function send_welcome_mail(string $email, string $name): bool
{
    $first = trim(explode(' ', trim($name))[0] ?? '') ?: 'there';
    return send_branded_mail(
        $email,
        'Welcome to Learn.Nectra',
        'Welcome, ' . $first . '!',
        "Your Learn.Nectra account is ready.\n\nEvery lesson is free to read. Pick a course, track your progress, pass the chapter quizzes, and earn your certificate.\n\nWant an ad-free experience with downloads and certificates? Packages start at Rs 199 for a full year.",
        'Start learning',
        site_base_url()
    );
}

function send_package_mail(PDO $pdo, int $studentId, int $subscriptionId): bool
{
    $stmt = $pdo->prepare("
        SELECT s.email, s.name, p.name AS plan_name, p.course_limit, ss.ends_at
        FROM student_subscriptions ss
        JOIN students s ON s.id = ss.student_id
        LEFT JOIN plans p ON p.id = ss.plan_id
        WHERE ss.id = :id AND ss.student_id = :student_id
        LIMIT 1
    ");
    $stmt->execute(['id' => $subscriptionId, 'student_id' => $studentId]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }
    $first = trim(explode(' ', trim((string)$row['name']))[0] ?? '') ?: 'there';
    $valid = $row['ends_at'] ? date('j M Y', strtotime((string)$row['ends_at'])) : 'no expiry';
    $scope = $row['course_limit'] === null
        ? 'Every course on Learn.Nectra is now ad-free for you, with downloads and certificates.'
        : 'You can use it on ' . (int)$row['course_limit'] . ' course' . ((int)$row['course_limit'] === 1 ? '' : 's') . '. Choose or review your courses on the Plans page.';
    return send_branded_mail(
        (string)$row['email'],
        'Your ' . $row['plan_name'] . ' package is active',
        'Your package is active',
        "Hi {$first},\n\nYour {$row['plan_name']} package is now active and valid until {$valid}.\n\n{$scope}\n\nThank you for learning with us.",
        'Open my packages',
        rtrim(site_base_url(), '/') . '/billing'
    );
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

function send_smtp_mail(string $to, string $subject, string $html, string $text = '', array $extraHeaders = []): bool
{
    $config = smtp_config();
    if ($config['password'] === '') {
        error_log('SMTP authentication is not configured.');
        return false;
    }
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
        ];
        foreach ($extraHeaders as $header) {
            $headers[] = str_replace(["\r", "\n"], '', (string)$header);
        }
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
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    return in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
        && preg_match('/^(?:localhost|127\.0\.0\.1|\[::1\])(?::[0-9]+)?$/', $host) === 1
        && getenv('ALLOW_LOCAL_DEV_OTP') === '1';
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

    $attempt = $pdo->prepare("UPDATE otp_verifications SET attempts = attempts + 1 WHERE id = :id AND attempts < 5 AND consumed_at IS NULL AND expires_at > NOW()");
    $attempt->execute(['id' => $row['id']]);
    if ($attempt->rowCount() !== 1) {
        return ['ok' => false, 'message' => 'This code is no longer available. Please request a new code.'];
    }
    if (!password_verify($otp, $row['otp_hash'])) {
        return ['ok' => false, 'message' => 'Invalid OTP. Please check the code.'];
    }

    $consume = $pdo->prepare("UPDATE otp_verifications SET consumed_at = NOW() WHERE id = :id AND consumed_at IS NULL AND expires_at > NOW()");
    $consume->execute(['id' => $row['id']]);
    if ($consume->rowCount() !== 1) {
        return ['ok' => false, 'message' => 'This code is no longer available. Please request a new code.'];
    }
    return ['ok' => true, 'row' => $row];
}
?>
