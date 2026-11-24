<?php
declare(strict_types=1);

function ensure_course_rank_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS course_rank (
            course_id INT NOT NULL PRIMARY KEY,
            opens INT UNSIGNED NOT NULL DEFAULT 0,
            searches INT UNSIGNED NOT NULL DEFAULT 0,
            reach INT UNSIGNED NOT NULL DEFAULT 0,
            heat DOUBLE NOT NULL DEFAULT 0,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $count = (int)$pdo->query('SELECT COUNT(*) FROM course_rank')->fetchColumn();
    if ($count === 0) {
        $pdo->exec("
            INSERT IGNORE INTO course_rank (course_id, opens, searches, reach, heat, updated_at)
            SELECT course_id,
                   COUNT(DISTINCT student_id),
                   0,
                   COUNT(*),
                   COUNT(DISTINCT student_id) * 5 + COUNT(*),
                   NOW()
            FROM student_progress
            GROUP BY course_id
        ");
    }
}

function course_rank_heat_sql(string $alias = 'r', bool $grouped = false): string
{
    if (!preg_match('/^[a-z]$/', $alias)) {
        $alias = 'r';
    }
    $heat = $grouped ? 'MAX(' . $alias . '.heat)' : $alias . '.heat';
    $updated = $grouped ? 'MAX(' . $alias . '.updated_at)' : $alias . '.updated_at';
    return 'COALESCE(' . $heat . ', 0) * POW(0.5, GREATEST(TIMESTAMPDIFF(MINUTE, COALESCE(' . $updated . ', UTC_TIMESTAMP()), UTC_TIMESTAMP()), 0) / 20160)';
}

function course_rank_decay_value(float $heat, string $updatedAt): float
{
    $then = strtotime($updatedAt);
    if ($then === false) {
        return $heat;
    }
    $minutes = max(0, (time() - $then) / 60);
    return $heat * (0.5 ** ($minutes / 20160));
}

function course_rank_query_counts(string $query): bool
{
    $length = function_exists('mb_strlen') ? mb_strlen($query, 'UTF-8') : strlen($query);
    return $length >= 3 && $length <= 80;
}

function course_rank_apply(PDO $pdo, int $courseId, string $signal): void
{
    $weights = ['open' => 5.0, 'search' => 3.0, 'reach' => 2.0];
    if ($courseId < 1 || !isset($weights[$signal])) {
        return;
    }

    $stmt = $pdo->prepare('SELECT heat, updated_at FROM course_rank WHERE course_id = ?');
    $stmt->execute([$courseId]);
    $row = $stmt->fetch();
    $heat = $row ? course_rank_decay_value((float)$row['heat'], (string)$row['updated_at']) : 0.0;
    $heat += $weights[$signal];

    $opens = $signal === 'open' ? 1 : 0;
    $searches = $signal === 'search' ? 1 : 0;
    $reach = $signal === 'reach' ? 1 : 0;
    $upd = $pdo->prepare('
        INSERT INTO course_rank (course_id, opens, searches, reach, heat, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            opens = opens + ?,
            searches = searches + ?,
            reach = reach + ?,
            heat = ?,
            updated_at = NOW()
    ');
    $upd->execute([$courseId, $opens, $searches, $reach, $heat, $opens, $searches, $reach, $heat]);
}

function record_course_signal(PDO $pdo, int $courseId, string $signal): void
{
    if (PHP_SAPI === 'cli' || $courseId < 1 || !in_array($signal, ['open', 'search', 'reach'], true)) {
        return;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return;
    }
    $purpose = strtolower((string)($_SERVER['HTTP_SEC_PURPOSE'] ?? $_SERVER['HTTP_PURPOSE'] ?? ''));
    if ($purpose === 'prefetch' || $purpose === 'preview') {
        return;
    }
    $agent = strtolower((string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
    if ($agent === '' || preg_match('/bot|spider|crawler|wget|curl|python-requests|headless/', $agent)) {
        return;
    }

    secure_session_start();
    $day = gmdate('Y-m-d');
    $seen = $_SESSION['course_rank_seen'] ?? null;
    if (!is_array($seen) || ($seen['day'] ?? '') !== $day) {
        $seen = ['day' => $day, 'open' => [], 'search' => [], 'reach' => []];
    }
    if (!empty($seen[$signal][$courseId])) {
        return;
    }
    if (!course_rank_take_slot($signal, $courseId)) {
        $seen[$signal][$courseId] = 1;
        $_SESSION['course_rank_seen'] = $seen;
        return;
    }

    try {
        course_rank_apply($pdo, $courseId, $signal);
        $seen[$signal][$courseId] = 1;
        $_SESSION['course_rank_seen'] = $seen;
    } catch (Throwable $e) {
        error_log('course rank: ' . $e->getMessage());
    }
}

function record_course_signals(PDO $pdo, array $courseIds, string $signal): void
{
    $unique = [];
    foreach ($courseIds as $courseId) {
        $courseId = (int)$courseId;
        if ($courseId > 0) {
            $unique[$courseId] = $courseId;
        }
    }
    foreach ($unique as $courseId) {
        record_course_signal($pdo, $courseId, $signal);
    }
}

function course_rank_referer_is_reach(): bool
{
    $referer = trim((string)($_SERVER['HTTP_REFERER'] ?? ''));
    if ($referer === '') {
        return true;
    }
    $parts = parse_url($referer);
    if (!is_array($parts) || empty($parts['host'])) {
        return true;
    }
    $host = strtolower((string)$parts['host']);
    $trusted = strtolower((string)preg_replace('/:\d+$/', '', trusted_request_host()));
    if ($host !== $trusted) {
        return true;
    }

    $path = rtrim((string)($parts['path'] ?? ''), '/');
    $path = (string)preg_replace('#/index(?:\.php)?$#i', '', $path);
    $path = rtrim($path, '/');
    if (preg_match('#/(?:(?:hi|bn|ta|te|mr|gu|pa)/)?search(?:\.php)?$#i', $path)) {
        return true;
    }
    return (bool)preg_match('#(?:^|/)(?:learning\.nectradigital\.com)?(?:/(?:hi|bn|ta|te|mr|gu|pa))?$#i', $path);
}

function course_rank_take_slot(string $signal, int $courseId): bool
{
    $address = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $packed = @inet_pton($address);
    $actor = hash('sha256', $packed !== false ? $packed : $address);
    $day = gmdate('Ymd');
    $cookieSent = isset($_COOKIE[session_name()]) && is_string($_COOKIE[session_name()]) && $_COOKIE[session_name()] !== '';
    $directory = __DIR__ . '/../sessions/course-rank-caps';
    if (!is_dir($directory) && !@mkdir($directory, 0700, true)) {
        return false;
    }

    $path = $directory . '/' . $day . '-' . substr($actor, 0, 2) . '.json';
    $handle = @fopen($path, 'c+');
    if (!$handle || !flock($handle, LOCK_EX)) {
        if ($handle) {
            fclose($handle);
        }
        return false;
    }

    $allowed = false;
    try {
        $entries = json_decode((string)stream_get_contents($handle), true);
        if (!is_array($entries)) {
            $entries = [];
        }
        $seenKey = $actor . '|' . $signal . '|' . $courseId;
        $capKey = $actor . '|n|' . $signal;
        if (!$cookieSent && !empty($entries[$seenKey])) {
            $allowed = false;
        } elseif ((int)($entries[$capKey] ?? 0) >= 80) {
            $allowed = false;
        } else {
            $entries[$seenKey] = 1;
            $entries[$capKey] = (int)($entries[$capKey] ?? 0) + 1;
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($entries));
            fflush($handle);
            $allowed = true;
        }
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    return $allowed;
}
