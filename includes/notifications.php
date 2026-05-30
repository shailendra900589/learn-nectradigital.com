<?php
declare(strict_types=1);

function ensure_notification_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $tableExists = function (string $table) use ($pdo): bool {
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table
        ");
        $stmt->execute(['table' => $table]);
        return (int)$stmt->fetchColumn() > 0;
    };
    $columnExists = function (string $table, string $column) use ($pdo): bool {
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table
              AND COLUMN_NAME = :column
        ");
        $stmt->execute(['table' => $table, 'column' => $column]);
        return (int)$stmt->fetchColumn() > 0;
    };

    if (!$tableExists('student_notifications')) {
        $pdo->exec("
            CREATE TABLE student_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NOT NULL,
                title VARCHAR(190) NOT NULL,
                message TEXT NOT NULL,
                type ENUM('system','progress','recommendation','billing','security') NOT NULL DEFAULT 'system',
                action_url VARCHAR(255) NULL,
                meta_json TEXT NULL,
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_notification_student (student_id, is_read, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    if (!$tableExists('notification_delivery_logs')) {
        $pdo->exec("
            CREATE TABLE notification_delivery_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NOT NULL,
                notification_id INT NULL,
                channel ENUM('inapp','email','digest') NOT NULL DEFAULT 'inapp',
                status ENUM('sent','skipped','failed') NOT NULL DEFAULT 'sent',
                context VARCHAR(120) NULL,
                detail TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_notification_delivery_student (student_id, channel, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    $studentNotificationColumns = [
        'notif_inapp_enabled' => "ALTER TABLE students ADD COLUMN notif_inapp_enabled TINYINT(1) NOT NULL DEFAULT 1 AFTER email_verified_at",
        'notif_email_enabled' => "ALTER TABLE students ADD COLUMN notif_email_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER notif_inapp_enabled",
        'notif_type_system' => "ALTER TABLE students ADD COLUMN notif_type_system TINYINT(1) NOT NULL DEFAULT 1 AFTER notif_email_enabled",
        'notif_type_progress' => "ALTER TABLE students ADD COLUMN notif_type_progress TINYINT(1) NOT NULL DEFAULT 1 AFTER notif_type_system",
        'notif_type_recommendation' => "ALTER TABLE students ADD COLUMN notif_type_recommendation TINYINT(1) NOT NULL DEFAULT 1 AFTER notif_type_progress",
        'notif_type_billing' => "ALTER TABLE students ADD COLUMN notif_type_billing TINYINT(1) NOT NULL DEFAULT 1 AFTER notif_type_recommendation",
        'notif_type_security' => "ALTER TABLE students ADD COLUMN notif_type_security TINYINT(1) NOT NULL DEFAULT 1 AFTER notif_type_billing",
        'notif_digest_frequency' => "ALTER TABLE students ADD COLUMN notif_digest_frequency ENUM('off','daily','weekly') NOT NULL DEFAULT 'off' AFTER notif_type_security",
        'notif_digest_weekday' => "ALTER TABLE students ADD COLUMN notif_digest_weekday TINYINT NOT NULL DEFAULT 1 AFTER notif_digest_frequency",
        'notif_digest_last_sent_at' => "ALTER TABLE students ADD COLUMN notif_digest_last_sent_at DATETIME NULL AFTER notif_digest_weekday",
    ];
    foreach ($studentNotificationColumns as $column => $sql) {
        if ($tableExists('students') && !$columnExists('students', $column)) {
            $pdo->exec($sql);
        }
    }
}

function log_notification_delivery(
    PDO $pdo,
    int $studentId,
    ?int $notificationId,
    string $channel,
    string $status,
    string $context = '',
    string $detail = ''
): void {
    $channel = in_array($channel, ['inapp', 'email', 'digest'], true) ? $channel : 'inapp';
    $status = in_array($status, ['sent', 'skipped', 'failed'], true) ? $status : 'sent';
    $stmt = $pdo->prepare("
        INSERT INTO notification_delivery_logs (student_id, notification_id, channel, status, context, detail)
        VALUES (:student_id, :notification_id, :channel, :status, :context, :detail)
    ");
    $stmt->execute([
        'student_id' => $studentId,
        'notification_id' => $notificationId,
        'channel' => $channel,
        'status' => $status,
        'context' => substr($context, 0, 120),
        'detail' => $detail ? seo_limit($detail, 1000) : null,
    ]);
}

function notification_type_allowed(PDO $pdo, int $studentId, string $type): bool
{
    $validType = in_array($type, ['system', 'progress', 'recommendation', 'billing', 'security'], true) ? $type : 'system';
    $column = 'notif_type_' . $validType;
    $stmt = $pdo->prepare("
        SELECT {$column}
        FROM students
        WHERE id = :student_id
        LIMIT 1
    ");
    $stmt->execute(['student_id' => $studentId]);
    return (int)($stmt->fetchColumn() ?? 1) === 1;
}

function notification_preferences(PDO $pdo, int $studentId): array
{
    $stmt = $pdo->prepare("
        SELECT notif_inapp_enabled, notif_email_enabled,
               notif_type_system, notif_type_progress, notif_type_recommendation, notif_type_billing, notif_type_security,
               notif_digest_frequency, notif_digest_weekday, notif_digest_last_sent_at
        FROM students
        WHERE id = :student_id
        LIMIT 1
    ");
    $stmt->execute(['student_id' => $studentId]);
    return $stmt->fetch() ?: [
        'notif_inapp_enabled' => 1,
        'notif_email_enabled' => 0,
        'notif_type_system' => 1,
        'notif_type_progress' => 1,
        'notif_type_recommendation' => 1,
        'notif_type_billing' => 1,
        'notif_type_security' => 1,
        'notif_digest_frequency' => 'off',
        'notif_digest_weekday' => 1,
        'notif_digest_last_sent_at' => null,
    ];
}

function update_notification_preferences(PDO $pdo, int $studentId, array $prefs): void
{
    $normalized = [
        'notif_inapp_enabled' => !empty($prefs['notif_inapp_enabled']) ? 1 : 0,
        'notif_email_enabled' => !empty($prefs['notif_email_enabled']) ? 1 : 0,
        'notif_type_system' => !empty($prefs['notif_type_system']) ? 1 : 0,
        'notif_type_progress' => !empty($prefs['notif_type_progress']) ? 1 : 0,
        'notif_type_recommendation' => !empty($prefs['notif_type_recommendation']) ? 1 : 0,
        'notif_type_billing' => !empty($prefs['notif_type_billing']) ? 1 : 0,
        'notif_type_security' => !empty($prefs['notif_type_security']) ? 1 : 0,
        'notif_digest_frequency' => in_array(($prefs['notif_digest_frequency'] ?? 'off'), ['off', 'daily', 'weekly'], true) ? $prefs['notif_digest_frequency'] : 'off',
        'notif_digest_weekday' => max(0, min(6, (int)($prefs['notif_digest_weekday'] ?? 1))),
    ];
    $stmt = $pdo->prepare("
        UPDATE students
        SET notif_inapp_enabled = :notif_inapp_enabled,
            notif_email_enabled = :notif_email_enabled,
            notif_type_system = :notif_type_system,
            notif_type_progress = :notif_type_progress,
            notif_type_recommendation = :notif_type_recommendation,
            notif_type_billing = :notif_type_billing,
            notif_type_security = :notif_type_security,
            notif_digest_frequency = :notif_digest_frequency,
            notif_digest_weekday = :notif_digest_weekday,
            updated_at = NOW()
        WHERE id = :student_id
    ");
    $stmt->execute($normalized + ['student_id' => $studentId]);
}

function send_notification_email(PDO $pdo, int $studentId, string $title, string $message, ?string $actionUrl = null): bool
{
    if (!function_exists('send_smtp_mail')) {
        require_once __DIR__ . '/mailer.php';
    }
    if (!function_exists('send_smtp_mail')) {
        return false;
    }

    $stmt = $pdo->prepare("SELECT email, name FROM students WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $studentId]);
    $student = $stmt->fetch();
    if (!$student || empty($student['email'])) {
        return false;
    }

    $safeTitle = seo_limit($title, 170);
    $safeMessage = seo_limit($message, 1000);
    $ctaHtml = $actionUrl ? '<p><a href="' . h((string)$actionUrl) . '" style="display:inline-block;padding:10px 16px;background:#2563eb;color:#fff;text-decoration:none;border-radius:8px;font-weight:700">Open</a></p>' : '';
    $html = '<p>Hello ' . h((string)$student['name']) . ',</p><p>' . h($safeMessage) . '</p>' . $ctaHtml . '<p style="color:#64748b">You can manage notification settings from your profile.</p>';
    $text = "Hello " . (string)$student['name'] . ",\n\n" . $safeMessage . ($actionUrl ? ("\n\nOpen: " . $actionUrl) : '') . "\n\nManage notification settings from your profile.";
    $sent = send_smtp_mail((string)$student['email'], '[Learn.Nectra] ' . $safeTitle, $html, $text);
    log_notification_delivery($pdo, $studentId, null, 'email', $sent ? 'sent' : 'failed', 'single_notification', $safeTitle);
    return $sent;
}

function send_notification_digest_email(PDO $pdo, int $studentId, array $digestItems, string $frequency): bool
{
    if (!$digestItems) {
        return false;
    }
    if (!function_exists('send_smtp_mail')) {
        require_once __DIR__ . '/mailer.php';
    }
    if (!function_exists('send_smtp_mail')) {
        return false;
    }

    $stmt = $pdo->prepare("SELECT email, name FROM students WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $studentId]);
    $student = $stmt->fetch();
    if (!$student || empty($student['email'])) {
        return false;
    }

    $title = $frequency === 'weekly' ? 'Your weekly learning digest' : 'Your daily learning digest';
    $lines = [];
    foreach ($digestItems as $item) {
        $lines[] = '- ' . plain_text((string)$item['title']) . ': ' . plain_text((string)$item['message']);
    }
    $text = "Hello " . (string)$student['name'] . ",\n\nHere is your " . $frequency . " digest:\n" . implode("\n", $lines) . "\n\nManage notification settings from your profile.";
    $htmlItems = '';
    foreach ($digestItems as $item) {
        $url = !empty($item['action_url']) ? '<a href="' . h((string)$item['action_url']) . '" style="color:#2563eb;font-weight:700">Open</a>' : '';
        $htmlItems .= '<li style="margin-bottom:10px"><strong>' . h((string)$item['title']) . '</strong><br>' . h((string)$item['message']) . '<br>' . $url . '</li>';
    }
    $html = '<p>Hello ' . h((string)$student['name']) . ',</p><p>Here is your ' . h($frequency) . ' digest:</p><ul>' . $htmlItems . '</ul><p style="color:#64748b">Manage notification settings from your profile.</p>';
    $sent = send_smtp_mail((string)$student['email'], '[Learn.Nectra] ' . $title, $html, $text);
    log_notification_delivery($pdo, $studentId, null, 'digest', $sent ? 'sent' : 'failed', $frequency . '_digest', $title);
    return $sent;
}

function run_due_notification_digests(PDO $pdo, int $batch = 80): array
{
    $batch = max(1, min(500, $batch));
    $todayDow = (int)date('w');

    $stmt = $pdo->prepare("
        SELECT id, notif_digest_frequency, notif_digest_weekday, notif_digest_last_sent_at
        FROM students
        WHERE notif_email_enabled = 1
          AND notif_digest_frequency IN ('daily', 'weekly')
        LIMIT {$batch}
    ");
    $stmt->execute();
    $students = $stmt->fetchAll() ?: [];

    $summary = ['checked' => count($students), 'eligible' => 0, 'sent' => 0, 'failed' => 0];
    foreach ($students as $student) {
        $studentId = (int)$student['id'];
        $frequency = (string)$student['notif_digest_frequency'];
        $lastSentAt = $student['notif_digest_last_sent_at'] ? strtotime((string)$student['notif_digest_last_sent_at']) : 0;
        $isDue = false;
        $windowHours = $frequency === 'weekly' ? 24 * 6 : 20;

        if ($frequency === 'daily') {
            $isDue = $lastSentAt === 0 || (time() - $lastSentAt) >= ($windowHours * 3600);
        } elseif ($frequency === 'weekly') {
            $weekday = max(0, min(6, (int)$student['notif_digest_weekday']));
            $isDue = ($todayDow === $weekday) && ($lastSentAt === 0 || (time() - $lastSentAt) >= (6 * 24 * 3600));
        }
        if (!$isDue) {
            continue;
        }

        $summary['eligible']++;
        $hoursLookback = $frequency === 'weekly' ? 24 * 7 : 24;
        $itemsStmt = $pdo->prepare("
            SELECT id, title, message, action_url
            FROM student_notifications
            WHERE student_id = :student_id
              AND created_at >= DATE_SUB(NOW(), INTERVAL {$hoursLookback} HOUR)
            ORDER BY created_at DESC
            LIMIT 8
        ");
        $itemsStmt->execute(['student_id' => $studentId]);
        $items = $itemsStmt->fetchAll() ?: [];
        if (!$items) {
            log_notification_delivery($pdo, $studentId, null, 'digest', 'skipped', $frequency . '_digest', 'No new notifications in window');
            continue;
        }

        $ok = send_notification_digest_email($pdo, $studentId, $items, $frequency);
        if ($ok) {
            $summary['sent']++;
            $upd = $pdo->prepare("UPDATE students SET notif_digest_last_sent_at = NOW(), updated_at = NOW() WHERE id = :id");
            $upd->execute(['id' => $studentId]);
        } else {
            $summary['failed']++;
        }
    }
    return $summary;
}

function create_notification(
    PDO $pdo,
    int $studentId,
    string $title,
    string $message,
    string $type = 'system',
    ?string $actionUrl = null,
    array $meta = []
): void {
    $type = in_array($type, ['system', 'progress', 'recommendation', 'billing', 'security'], true) ? $type : 'system';
    $prefs = notification_preferences($pdo, $studentId);
    if (!notification_type_allowed($pdo, $studentId, $type)) {
        log_notification_delivery($pdo, $studentId, null, 'inapp', 'skipped', 'type_disabled', $type);
        return;
    }

    $notificationId = null;
    if ((int)$prefs['notif_inapp_enabled'] === 1) {
    $stmt = $pdo->prepare("
        INSERT INTO student_notifications (student_id, title, message, type, action_url, meta_json)
        VALUES (:student_id, :title, :message, :type, :action_url, :meta_json)
    ");
    $stmt->execute([
        'student_id' => $studentId,
        'title' => seo_limit($title, 180),
        'message' => seo_limit($message, 800),
            'type' => $type,
        'action_url' => $actionUrl ? substr($actionUrl, 0, 255) : null,
        'meta_json' => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
    ]);
    $notificationId = (int)$pdo->lastInsertId();
    log_notification_delivery($pdo, $studentId, $notificationId, 'inapp', 'sent', 'create_notification', $type);
    } else {
        log_notification_delivery($pdo, $studentId, null, 'inapp', 'skipped', 'inapp_disabled', $type);
    }

    if ((int)$prefs['notif_email_enabled'] === 1) {
        $sent = send_notification_email($pdo, $studentId, $title, $message, $actionUrl);
        if (!$sent) {
            log_notification_delivery($pdo, $studentId, $notificationId, 'email', 'failed', 'create_notification', $type);
        }
    } else {
        log_notification_delivery($pdo, $studentId, $notificationId, 'email', 'skipped', 'email_disabled', $type);
    }
}

function unread_notifications_count(PDO $pdo, int $studentId): int
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM student_notifications
        WHERE student_id = :student_id
          AND is_read = 0
    ");
    $stmt->execute(['student_id' => $studentId]);
    return (int)$stmt->fetchColumn();
}

function recent_notifications(PDO $pdo, int $studentId, int $limit = 8): array
{
    $limit = max(1, min(20, $limit));
    $stmt = $pdo->prepare("
        SELECT id, title, message, type, action_url, meta_json, is_read, created_at
        FROM student_notifications
        WHERE student_id = :student_id
        ORDER BY created_at DESC, id DESC
        LIMIT {$limit}
    ");
    $stmt->execute(['student_id' => $studentId]);
    $rows = $stmt->fetchAll() ?: [];
    foreach ($rows as &$row) {
        $row['meta'] = json_decode((string)($row['meta_json'] ?? ''), true) ?: [];
        unset($row['meta_json']);
    }
    unset($row);
    return $rows;
}

function mark_notifications_read(PDO $pdo, int $studentId, array $ids = []): void
{
    $ids = array_values(array_filter(array_map('intval', $ids), fn($id) => $id > 0));
    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge([$studentId], $ids);
        $stmt = $pdo->prepare("
            UPDATE student_notifications
            SET is_read = 1
            WHERE student_id = ?
              AND id IN ({$placeholders})
        ");
        $stmt->execute($params);
        return;
    }

    $stmt = $pdo->prepare("
        UPDATE student_notifications
        SET is_read = 1
        WHERE student_id = :student_id
          AND is_read = 0
    ");
    $stmt->execute(['student_id' => $studentId]);
}
?>
