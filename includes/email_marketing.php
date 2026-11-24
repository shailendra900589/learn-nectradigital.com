<?php
declare(strict_types=1);

const EMAIL_DAILY_CAP_DEFAULT = 450;
const EMAIL_BATCH_SIZE = 12;

function email_audiences(): array
{
    return [
        'all' => 'All verified students',
        'free' => 'Free users (no active package)',
        'paid' => 'Students with an active package',
    ];
}

function email_audience_sql(string $audience): string
{
    $base = "FROM students s WHERE s.email_verified = 1 AND s.marketing_opt_out = 0 AND s.account_status = 'active'";
    $hasPackage = "EXISTS (SELECT 1 FROM student_subscriptions ss WHERE ss.student_id = s.id AND ss.status = 'active' AND (ss.ends_at IS NULL OR ss.ends_at >= NOW()))";
    if ($audience === 'free') {
        return $base . " AND NOT " . $hasPackage;
    }
    if ($audience === 'paid') {
        return $base . " AND " . $hasPackage;
    }
    return $base;
}

function email_audience_count(PDO $pdo, string $audience): int
{
    return (int)$pdo->query('SELECT COUNT(*) ' . email_audience_sql($audience))->fetchColumn();
}

function email_unsubscribe_token(int $studentId): string
{
    return substr(hash_hmac('sha256', 'unsubscribe|' . $studentId, asset_signing_secret()), 0, 32);
}

function email_unsubscribe_url(int $studentId): string
{
    return rtrim(site_base_url(), '/') . '/unsubscribe?u=' . $studentId . '&t=' . email_unsubscribe_token($studentId);
}

function email_daily_cap(PDO $pdo): int
{
    return max(1, (int)platform_setting($pdo, 'email_daily_cap', (string)EMAIL_DAILY_CAP_DEFAULT));
}

function email_sent_today(PDO $pdo): int
{
    return (int)$pdo->query("SELECT COUNT(*) FROM email_queue WHERE status = 'sent' AND sent_at >= CURDATE()")->fetchColumn();
}

function create_email_campaign(PDO $pdo, array $data, string $audience, int $adminId): array
{
    $pdo->prepare("
        INSERT INTO email_campaigns (subject, heading, body, cta_text, cta_url, audience, created_by)
        VALUES (:subject, :heading, :body, :cta_text, :cta_url, :audience, :admin)
    ")->execute([
        'subject' => $data['subject'],
        'heading' => $data['heading'],
        'body' => $data['body'],
        'cta_text' => $data['cta_text'] ?: null,
        'cta_url' => $data['cta_url'] ?: null,
        'audience' => $audience,
        'admin' => $adminId ?: null,
    ]);
    $campaignId = (int)$pdo->lastInsertId();
    $stmt = $pdo->prepare('INSERT IGNORE INTO email_queue (campaign_id, student_id, email) SELECT :cid, s.id, s.email ' . email_audience_sql($audience));
    $stmt->execute(['cid' => $campaignId]);
    $total = $stmt->rowCount();
    $pdo->prepare("UPDATE email_campaigns SET total = :total, status = IF(:total2 = 0, 'done', 'sending'), finished_at = IF(:total3 = 0, NOW(), NULL) WHERE id = :id")
        ->execute(['total' => $total, 'total2' => $total, 'total3' => $total, 'id' => $campaignId]);
    return ['id' => $campaignId, 'total' => $total];
}

function send_campaign_mail(array $campaign, string $email, string $name, int $studentId): bool
{
    $first = trim(explode(' ', trim($name))[0] ?? '') ?: 'there';
    $replace = ['{name}' => $first, '{first_name}' => $first];
    $unsubscribe = $studentId > 0 ? email_unsubscribe_url($studentId) : rtrim(site_base_url(), '/');
    $footer = 'You are receiving this because you have a Learn.Nectra account. <a href="' . h($unsubscribe) . '" style="color:#64748b">Unsubscribe</a>';
    $html = email_layout(
        strtr((string)$campaign['heading'], $replace),
        email_paragraphs(strtr((string)$campaign['body'], $replace)),
        (string)($campaign['cta_text'] ?? ''),
        (string)($campaign['cta_url'] ?? ''),
        $footer
    );
    $text = strtr((string)$campaign['heading'], $replace) . "\n\n" . strtr((string)$campaign['body'], $replace)
        . (!empty($campaign['cta_url']) ? "\n\n" . $campaign['cta_text'] . ': ' . $campaign['cta_url'] : '')
        . "\n\nUnsubscribe: " . $unsubscribe;
    return send_smtp_mail($email, strtr((string)$campaign['subject'], $replace), $html, $text, [
        'List-Unsubscribe: <' . $unsubscribe . '>',
        'List-Unsubscribe-Post: List-Unsubscribe=One-Click',
        'Precedence: bulk',
    ]);
}

function process_email_queue(PDO $pdo, int $limit = EMAIL_BATCH_SIZE): int
{
    $lockPath = dirname(__DIR__) . '/sessions/.email-queue.lock';
    $lock = @fopen($lockPath, 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
        return 0;
    }
    $sent = 0;
    try {
        $room = email_daily_cap($pdo) - email_sent_today($pdo);
        $limit = min($limit, $room);
        if ($limit <= 0) {
            return 0;
        }
        $rows = $pdo->query("
            SELECT q.id, q.campaign_id, q.student_id, q.email, s.name, s.marketing_opt_out
            FROM email_queue q
            JOIN email_campaigns c ON c.id = q.campaign_id AND c.status = 'sending'
            LEFT JOIN students s ON s.id = q.student_id
            WHERE q.status = 'queued'
            ORDER BY q.id ASC
            LIMIT " . (int)$limit
        )->fetchAll();
        $campaigns = [];
        foreach ($rows as $row) {
            $cid = (int)$row['campaign_id'];
            if (!isset($campaigns[$cid])) {
                $stmt = $pdo->prepare('SELECT * FROM email_campaigns WHERE id = :id');
                $stmt->execute(['id' => $cid]);
                $campaigns[$cid] = $stmt->fetch();
            }
            if ((int)($row['marketing_opt_out'] ?? 0) === 1 || $row['name'] === null) {
                $pdo->prepare("UPDATE email_queue SET status = 'skipped' WHERE id = :id")->execute(['id' => $row['id']]);
                continue;
            }
            $ok = send_campaign_mail($campaigns[$cid], (string)$row['email'], (string)$row['name'], (int)$row['student_id']);
            $pdo->prepare("UPDATE email_queue SET attempts = attempts + 1, status = :status, sent_at = IF(:ok = 1, NOW(), sent_at) WHERE id = :id")
                ->execute(['status' => $ok ? 'sent' : 'failed', 'ok' => $ok ? 1 : 0, 'id' => $row['id']]);
            $pdo->prepare('UPDATE email_campaigns SET ' . ($ok ? 'sent = sent + 1' : 'failed = failed + 1') . ' WHERE id = :id')->execute(['id' => $cid]);
            if ($ok) {
                $sent++;
            }
        }
        $pdo->exec("
            UPDATE email_campaigns c
            SET c.status = 'done', c.finished_at = NOW()
            WHERE c.status = 'sending'
              AND NOT EXISTS (SELECT 1 FROM email_queue q WHERE q.campaign_id = c.id AND q.status = 'queued')
        ");
    } catch (Throwable $e) {
        error_log('Email queue failed: ' . $e->getMessage());
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
    return $sent;
}

function email_queue_tick(PDO $pdo): void
{
    if (PHP_SAPI === 'cli') {
        return;
    }
    $marker = dirname(__DIR__) . '/sessions/.email-queue-at';
    $last = @filemtime($marker);
    if ($last !== false && time() - $last < 60) {
        return;
    }
    @touch($marker);
    try {
        if (!(int)$pdo->query("SELECT EXISTS (SELECT 1 FROM email_queue WHERE status = 'queued')")->fetchColumn()) {
            return;
        }
    } catch (Throwable $e) {
        return;
    }
    defer_after_response(static fn() => process_email_queue($pdo));
}
