<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

if (!getenv('SITE_URL')) {
    putenv('SITE_URL=https://learning.nectradigital.com');
}

require_once __DIR__ . '/includes/db.php';

$sent = process_email_queue($pdo, EMAIL_BATCH_SIZE);
if ($sent > 0 || smtp_last_error() !== '') {
    echo date('Y-m-d H:i:s') . ' sent=' . $sent . (smtp_last_error() !== '' ? ' error=' . smtp_last_error() : '') . "\n";
}
