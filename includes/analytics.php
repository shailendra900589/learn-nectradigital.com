<?php
declare(strict_types=1);

function ensure_analytics_schema(PDO $pdo): void
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

    if (!$tableExists('site_visitors')) {
        $pdo->exec("
            CREATE TABLE site_visitors (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visitor_token CHAR(64) NOT NULL UNIQUE,
                first_seen DATETIME NOT NULL,
                last_seen DATETIME NOT NULL,
                total_hits INT NOT NULL DEFAULT 1,
                country_code CHAR(2) NULL,
                device_type VARCHAR(20) NULL,
                browser_family VARCHAR(30) NULL,
                last_ip_hash CHAR(64) NULL,
                last_user_agent_hash CHAR(64) NULL,
                INDEX idx_visitor_seen (last_seen),
                INDEX idx_visitor_country (country_code),
                INDEX idx_visitor_device (device_type),
                INDEX idx_visitor_browser (browser_family)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } else {
        $columns = [
            'country_code' => "ALTER TABLE site_visitors ADD COLUMN country_code CHAR(2) NULL AFTER total_hits",
            'device_type' => "ALTER TABLE site_visitors ADD COLUMN device_type VARCHAR(20) NULL AFTER country_code",
            'browser_family' => "ALTER TABLE site_visitors ADD COLUMN browser_family VARCHAR(30) NULL AFTER device_type",
            'last_ip_hash' => "ALTER TABLE site_visitors ADD COLUMN last_ip_hash CHAR(64) NULL AFTER browser_family",
            'last_user_agent_hash' => "ALTER TABLE site_visitors ADD COLUMN last_user_agent_hash CHAR(64) NULL AFTER last_ip_hash",
        ];
        foreach ($columns as $column => $sql) {
            if (!$columnExists('site_visitors', $column)) {
                $pdo->exec($sql);
            }
        }
    }

    if (!$tableExists('site_metrics')) {
        $pdo->exec("
            CREATE TABLE site_metrics (
                metric_key VARCHAR(80) PRIMARY KEY,
                metric_value BIGINT NOT NULL DEFAULT 0,
                updated_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('site_visitor_daily')) {
        $pdo->exec("
            CREATE TABLE site_visitor_daily (
                visit_date DATE NOT NULL,
                visitor_token CHAR(64) NOT NULL,
                first_seen_at DATETIME NOT NULL,
                PRIMARY KEY (visit_date, visitor_token),
                INDEX idx_visit_date (visit_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('site_traffic_daily')) {
        $pdo->exec("
            CREATE TABLE site_traffic_daily (
                visit_date DATE PRIMARY KEY,
                total_hits BIGINT NOT NULL DEFAULT 0,
                updated_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('site_metrics_cache')) {
        $pdo->exec("
            CREATE TABLE site_metrics_cache (
                cache_key VARCHAR(120) PRIMARY KEY,
                payload_json MEDIUMTEXT NOT NULL,
                expires_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX idx_cache_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('site_geoip_cache')) {
        $pdo->exec("
            CREATE TABLE site_geoip_cache (
                ip_hash CHAR(64) PRIMARY KEY,
                country_code CHAR(2) NOT NULL DEFAULT 'UN',
                source VARCHAR(40) NOT NULL DEFAULT 'fallback',
                updated_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('site_visitor_hourly')) {
        $pdo->exec("
            CREATE TABLE site_visitor_hourly (
                hour_bucket DATETIME NOT NULL,
                visitor_token CHAR(64) NOT NULL,
                first_seen_at DATETIME NOT NULL,
                PRIMARY KEY (hour_bucket, visitor_token),
                INDEX idx_hour_bucket (hour_bucket)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('site_traffic_hourly')) {
        $pdo->exec("
            CREATE TABLE site_traffic_hourly (
                hour_bucket DATETIME PRIMARY KEY,
                total_hits BIGINT NOT NULL DEFAULT 0,
                unique_visitors BIGINT NOT NULL DEFAULT 0,
                updated_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    $stmtSeed = $pdo->prepare("
        INSERT IGNORE INTO site_metrics (metric_key, metric_value, updated_at)
        VALUES ('lifetime_visitors', 0, NOW())
    ");
    $stmtSeed->execute();
}

function should_track_lifetime_visit(): bool
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return false;
    }

    $script = strtolower((string)basename($_SERVER['SCRIPT_NAME'] ?? ''));
    $skipScripts = [
        'feed.php',
        'google_feed.php',
        'recommendations_feed.php',
        'sitemap.php',
        'opensearch.xml.php',
        'search_ajax.php',
        'notifications_api.php',
        'notifications_digest_runner.php',
    ];
    if (in_array($script, $skipScripts, true)) {
        return false;
    }

    $path = strtolower((string)($_SERVER['REQUEST_URI'] ?? ''));
    if (str_contains($path, '/admin/')) {
        return false;
    }

    return true;
}

function is_known_bot_user_agent(?string $userAgent): bool
{
    $ua = strtolower(trim((string)$userAgent));
    if ($ua === '') {
        return false;
    }
    $botKeywords = [
        'bot', 'crawl', 'spider', 'slurp', 'bingpreview', 'facebookexternalhit', 'whatsapp', 'telegrambot',
        'linkedinbot', 'pinterest', 'duckduckbot', 'semrush', 'ahrefs', 'mj12bot', 'screaming frog',
        'yandex', 'baiduspider', 'applebot', 'googlebot', 'bingbot',
    ];
    foreach ($botKeywords as $keyword) {
        if (str_contains($ua, $keyword)) {
            return true;
        }
    }
    return false;
}

function normalize_client_ip(): string
{
    $candidates = [];
    $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if (is_string($forwarded) && $forwarded !== '') {
        foreach (explode(',', $forwarded) as $part) {
            $part = trim($part);
            if ($part !== '') {
                $candidates[] = $part;
            }
        }
    }

    $headers = [
        $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '',
        $_SERVER['HTTP_X_REAL_IP'] ?? '',
        $_SERVER['HTTP_CLIENT_IP'] ?? '',
        $_SERVER['REMOTE_ADDR'] ?? '',
    ];
    foreach ($headers as $candidate) {
        if (is_string($candidate) && trim($candidate) !== '') {
            $candidates[] = trim($candidate);
        }
    }

    foreach ($candidates as $candidate) {
        if (!filter_var($candidate, FILTER_VALIDATE_IP)) {
            continue;
        }
        if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $candidate;
        }
    }
    foreach ($candidates as $candidate) {
        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            return $candidate;
        }
    }
    return '0.0.0.0';
}

function geoip_cache_country(PDO $pdo, string $ipHash): ?string
{
    $stmt = $pdo->prepare("
        SELECT country_code
        FROM site_geoip_cache
        WHERE ip_hash = :ip_hash
          AND updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        LIMIT 1
    ");
    $stmt->execute(['ip_hash' => $ipHash]);
    $value = strtoupper((string)($stmt->fetchColumn() ?: ''));
    return preg_match('/^[A-Z]{2}$/', $value) ? $value : null;
}

function geoip_cache_store(PDO $pdo, string $ipHash, string $countryCode, string $source): void
{
    $countryCode = preg_match('/^[A-Z]{2}$/', $countryCode) ? $countryCode : 'UN';
    $source = seo_limit($source, 40);
    $stmt = $pdo->prepare("
        INSERT INTO site_geoip_cache (ip_hash, country_code, source, updated_at)
        VALUES (:ip_hash, :country_code, :source, NOW())
        ON DUPLICATE KEY UPDATE
            country_code = VALUES(country_code),
            source = VALUES(source),
            updated_at = NOW()
    ");
    $stmt->execute([
        'ip_hash' => $ipHash,
        'country_code' => $countryCode,
        'source' => $source,
    ]);
}

function fetch_country_code_from_geoip_api(string $ip): ?string
{
    $template = trim((string)(getenv('GEOIP_API_URL_TEMPLATE') ?: ''));
    if ($template === '' || !str_contains($template, '{ip}')) {
        return null;
    }
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return null;
    }

    $url = str_replace('{ip}', rawurlencode($ip), $template);
    $context = stream_context_create([
        'http' => ['timeout' => 1.5, 'ignore_errors' => true],
        'https' => ['timeout' => 1.5, 'ignore_errors' => true],
    ]);
    $response = @file_get_contents($url, false, $context);
    if ($response === false || $response === '') {
        return null;
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        return null;
    }
    $candidateKeys = ['countryCode', 'country_code', 'country'];
    foreach ($candidateKeys as $key) {
        if (!isset($decoded[$key])) {
            continue;
        }
        $value = strtoupper(substr(trim((string)$decoded[$key]), 0, 2));
        if (preg_match('/^[A-Z]{2}$/', $value)) {
            return $value;
        }
    }
    return null;
}

function infer_country_code(PDO $pdo, string $ip, string $ipHash): string
{
    $candidates = [
        $_SERVER['HTTP_CF_IPCOUNTRY'] ?? '',
        $_SERVER['GEOIP_COUNTRY_CODE'] ?? '',
        $_SERVER['HTTP_X_APPENGINE_COUNTRY'] ?? '',
    ];
    foreach ($candidates as $value) {
        $value = strtoupper(trim((string)$value));
        if (preg_match('/^[A-Z]{2}$/', $value)) {
            geoip_cache_store($pdo, $ipHash, $value, 'header');
            return $value;
        }
    }

    $cached = geoip_cache_country($pdo, $ipHash);
    if ($cached !== null) {
        return $cached;
    }

    $apiCountry = fetch_country_code_from_geoip_api($ip);
    if ($apiCountry !== null) {
        geoip_cache_store($pdo, $ipHash, $apiCountry, 'api');
        return $apiCountry;
    }

    $acceptLanguage = (string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
    if (preg_match('/-[A-Za-z]{2}\b/', $acceptLanguage, $match)) {
        $fallback = strtoupper(substr($match[0], 1, 2));
        geoip_cache_store($pdo, $ipHash, $fallback, 'accept-language');
        return $fallback;
    }
    geoip_cache_store($pdo, $ipHash, 'UN', 'fallback');
    return 'UN';
}

function infer_device_type(string $userAgent): string
{
    $ua = strtolower($userAgent);
    if ($ua === '') {
        return 'unknown';
    }
    if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet')) {
        return 'tablet';
    }
    if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
        return 'mobile';
    }
    return 'desktop';
}

function infer_browser_family(string $userAgent): string
{
    $ua = strtolower($userAgent);
    if (str_contains($ua, 'edg/')) return 'edge';
    if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) return 'opera';
    if (str_contains($ua, 'firefox/')) return 'firefox';
    if (str_contains($ua, 'safari/') && !str_contains($ua, 'chrome/')) return 'safari';
    if (str_contains($ua, 'chrome/')) return 'chrome';
    return 'other';
}

function cache_get(PDO $pdo, string $key)
{
    $stmt = $pdo->prepare("
        SELECT payload_json
        FROM site_metrics_cache
        WHERE cache_key = :cache_key
          AND expires_at > NOW()
        LIMIT 1
    ");
    $stmt->execute(['cache_key' => $key]);
    $json = $stmt->fetchColumn();
    if ($json === false) {
        return null;
    }
    $decoded = json_decode((string)$json, true);
    return is_array($decoded) ? $decoded : null;
}

function cache_set(PDO $pdo, string $key, $value, int $ttlSeconds = 60): void
{
    $ttlSeconds = max(5, min(3600, $ttlSeconds));
    $stmt = $pdo->prepare("
        INSERT INTO site_metrics_cache (cache_key, payload_json, expires_at, updated_at)
        VALUES (:cache_key, :payload_json, DATE_ADD(NOW(), INTERVAL :ttl SECOND), NOW())
        ON DUPLICATE KEY UPDATE
            payload_json = VALUES(payload_json),
            expires_at = VALUES(expires_at),
            updated_at = NOW()
    ");
    $stmt->execute([
        'cache_key' => $key,
        'payload_json' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'ttl' => $ttlSeconds,
    ]);
}

function invalidate_visitor_cache(PDO $pdo): void
{
    $stmt = $pdo->prepare("DELETE FROM site_metrics_cache WHERE cache_key LIKE 'visitor_%'");
    $stmt->execute();
}

function upsert_daily_visitor(PDO $pdo, string $token): void
{
    $daily = $pdo->prepare("
        INSERT IGNORE INTO site_visitor_daily (visit_date, visitor_token, first_seen_at)
        VALUES (CURDATE(), :token, NOW())
    ");
    $daily->execute(['token' => $token]);
}

function upsert_daily_hits(PDO $pdo): void
{
    $hits = $pdo->prepare("
        INSERT INTO site_traffic_daily (visit_date, total_hits, updated_at)
        VALUES (CURDATE(), 1, NOW())
        ON DUPLICATE KEY UPDATE total_hits = total_hits + 1, updated_at = NOW()
    ");
    $hits->execute();
}

function upsert_hourly_traffic(PDO $pdo, string $visitorToken): void
{
    $hourStmt = $pdo->prepare("
        INSERT INTO site_traffic_hourly (hour_bucket, total_hits, unique_visitors, updated_at)
        VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), 1, 0, NOW())
        ON DUPLICATE KEY UPDATE total_hits = total_hits + 1, updated_at = NOW()
    ");
    $hourStmt->execute();

    $uniqueMark = $pdo->prepare("
        INSERT IGNORE INTO site_visitor_hourly (hour_bucket, visitor_token, first_seen_at)
        VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), :visitor_token, NOW())
    ");
    $uniqueMark->execute(['visitor_token' => $visitorToken]);

    if ($uniqueMark->rowCount() > 0) {
        $incUnique = $pdo->prepare("
            UPDATE site_traffic_hourly
            SET unique_visitors = unique_visitors + 1, updated_at = NOW()
            WHERE hour_bucket = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')
        ");
        $incUnique->execute();
    }
}

function track_lifetime_visitor(PDO $pdo): void
{
    if (!should_track_lifetime_visit()) {
        return;
    }
    if (is_known_bot_user_agent($_SERVER['HTTP_USER_AGENT'] ?? '')) {
        return;
    }

    $userAgent = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
    $clientIp = normalize_client_ip();
    $deviceType = infer_device_type($userAgent);
    $browserFamily = infer_browser_family($userAgent);
    $ipHash = hash('sha256', $clientIp);
    $countryCode = infer_country_code($pdo, $clientIp, $ipHash);
    $uaHash = hash('sha256', $userAgent);

    $cookieName = 'ln_visitor_token';
    $token = (string)($_COOKIE[$cookieName] ?? '');
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        $token = hash('sha256', bin2hex(random_bytes(24)) . '|' . $clientIp . '|' . microtime(true));
        setcookie($cookieName, $token, [
            'expires' => time() + (86400 * 3650),
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    $stmt = $pdo->prepare("SELECT id FROM site_visitors WHERE visitor_token = :token LIMIT 1");
    $stmt->execute(['token' => $token]);
    $existingId = (int)($stmt->fetchColumn() ?: 0);

    if ($existingId > 0) {
        $update = $pdo->prepare("
            UPDATE site_visitors
            SET last_seen = NOW(),
                total_hits = total_hits + 1,
                country_code = :country_code,
                device_type = :device_type,
                browser_family = :browser_family,
                last_ip_hash = :last_ip_hash,
                last_user_agent_hash = :last_user_agent_hash
            WHERE id = :id
        ");
        $update->execute([
            'id' => $existingId,
            'country_code' => $countryCode,
            'device_type' => $deviceType,
            'browser_family' => $browserFamily,
            'last_ip_hash' => $ipHash,
            'last_user_agent_hash' => $uaHash,
        ]);
        upsert_daily_visitor($pdo, $token);
        upsert_daily_hits($pdo);
        upsert_hourly_traffic($pdo, $token);
        invalidate_visitor_cache($pdo);
        return;
    }

    $insert = $pdo->prepare("
        INSERT INTO site_visitors (
            visitor_token, first_seen, last_seen, total_hits,
            country_code, device_type, browser_family, last_ip_hash, last_user_agent_hash
        )
        VALUES (
            :token, NOW(), NOW(), 1,
            :country_code, :device_type, :browser_family, :last_ip_hash, :last_user_agent_hash
        )
    ");
    $insert->execute([
        'token' => $token,
        'country_code' => $countryCode,
        'device_type' => $deviceType,
        'browser_family' => $browserFamily,
        'last_ip_hash' => $ipHash,
        'last_user_agent_hash' => $uaHash,
    ]);

    $counter = $pdo->prepare("
        UPDATE site_metrics
        SET metric_value = metric_value + 1, updated_at = NOW()
        WHERE metric_key = 'lifetime_visitors'
    ");
    $counter->execute();

    upsert_daily_visitor($pdo, $token);
    upsert_daily_hits($pdo);
    upsert_hourly_traffic($pdo, $token);
    invalidate_visitor_cache($pdo);
}

function lifetime_visitor_count(PDO $pdo): int
{
    $stmt = $pdo->prepare("SELECT metric_value FROM site_metrics WHERE metric_key = 'lifetime_visitors' LIMIT 1");
    $stmt->execute();
    $value = (int)($stmt->fetchColumn() ?: 0);
    if ($value > 0) {
        return $value;
    }
    $fallback = $pdo->query("SELECT COUNT(*) FROM site_visitors")->fetchColumn();
    return (int)($fallback ?: 0);
}

function visitor_metrics_summary(PDO $pdo): array
{
    $lifetime = lifetime_visitor_count($pdo);

    $todayStmt = $pdo->query("
        SELECT COUNT(DISTINCT visitor_token)
        FROM site_visitor_daily
        WHERE visit_date = CURDATE()
    ");
    $todayUnique = (int)($todayStmt->fetchColumn() ?: 0);

    $monthStmt = $pdo->query("
        SELECT COUNT(DISTINCT visitor_token)
        FROM site_visitor_daily
        WHERE visit_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
          AND visit_date <= CURDATE()
    ");
    $monthUnique = (int)($monthStmt->fetchColumn() ?: 0);

    $liveStmt = $pdo->query("
        SELECT COUNT(*)
        FROM site_visitors
        WHERE last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ");
    $liveOnline = (int)($liveStmt->fetchColumn() ?: 0);

    return [
        'lifetime' => $lifetime,
        'today_unique' => $todayUnique,
        'month_unique' => $monthUnique,
        'live_online' => $liveOnline,
    ];
}

function cached_visitor_metrics_summary(PDO $pdo, int $ttlSeconds = 60): array
{
    $cacheKey = 'visitor_summary';
    $cached = cache_get($pdo, $cacheKey);
    if ($cached !== null) {
        return $cached;
    }
    $value = visitor_metrics_summary($pdo);
    cache_set($pdo, $cacheKey, $value, $ttlSeconds);
    return $value;
}

function visitor_trend_last_days(PDO $pdo, int $days = 7): array
{
    $days = max(2, min(60, $days));
    $stmt = $pdo->prepare("
        SELECT d.visit_date,
               COUNT(DISTINCT d.visitor_token) AS unique_visitors,
               COALESCE(t.total_hits, 0) AS total_hits
        FROM site_visitor_daily d
        LEFT JOIN site_traffic_daily t ON t.visit_date = d.visit_date
        WHERE d.visit_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        GROUP BY d.visit_date, t.total_hits
        ORDER BY d.visit_date ASC
    ");
    $stmt->execute(['days' => $days - 1]);
    $rows = $stmt->fetchAll() ?: [];

    $map = [];
    foreach ($rows as $row) {
        $map[(string)$row['visit_date']] = [
            'date' => (string)$row['visit_date'],
            'unique_visitors' => (int)$row['unique_visitors'],
            'total_hits' => (int)$row['total_hits'],
        ];
    }

    $result = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} day"));
        $result[] = $map[$date] ?? ['date' => $date, 'unique_visitors' => 0, 'total_hits' => 0];
    }
    return $result;
}

function cached_visitor_trend_last_days(PDO $pdo, int $days = 7, int $ttlSeconds = 120): array
{
    $cacheKey = 'visitor_trend_' . $days;
    $cached = cache_get($pdo, $cacheKey);
    if ($cached !== null) {
        return $cached;
    }
    $value = visitor_trend_last_days($pdo, $days);
    cache_set($pdo, $cacheKey, $value, $ttlSeconds);
    return $value;
}

function visitor_hourly_trend_today(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(hour_bucket, '%H:00') AS hour_label,
               total_hits,
               unique_visitors
        FROM site_traffic_hourly
        WHERE DATE(hour_bucket) = CURDATE()
        ORDER BY hour_bucket ASC
    ");
    $rows = $stmt->fetchAll() ?: [];
    $map = [];
    foreach ($rows as $row) {
        $map[(string)$row['hour_label']] = [
            'hour' => (string)$row['hour_label'],
            'hits' => (int)$row['total_hits'],
            'unique' => (int)$row['unique_visitors'],
        ];
    }

    $output = [];
    for ($h = 0; $h < 24; $h++) {
        $label = str_pad((string)$h, 2, '0', STR_PAD_LEFT) . ':00';
        $output[] = $map[$label] ?? ['hour' => $label, 'hits' => 0, 'unique' => 0];
    }
    return $output;
}

function cached_visitor_hourly_trend_today(PDO $pdo, int $ttlSeconds = 120): array
{
    $cacheKey = 'visitor_hourly_today';
    $cached = cache_get($pdo, $cacheKey);
    if ($cached !== null) {
        return $cached;
    }
    $value = visitor_hourly_trend_today($pdo);
    cache_set($pdo, $cacheKey, $value, $ttlSeconds);
    return $value;
}

function visitor_distribution_summary(PDO $pdo): array
{
    $countries = $pdo->query("
        SELECT COALESCE(NULLIF(country_code, ''), 'UN') AS label, COUNT(*) AS count
        FROM site_visitors
        GROUP BY label
        ORDER BY count DESC
        LIMIT 8
    ")->fetchAll() ?: [];

    $devices = $pdo->query("
        SELECT COALESCE(NULLIF(device_type, ''), 'unknown') AS label, COUNT(*) AS count
        FROM site_visitors
        GROUP BY label
        ORDER BY count DESC
        LIMIT 6
    ")->fetchAll() ?: [];

    $browsers = $pdo->query("
        SELECT COALESCE(NULLIF(browser_family, ''), 'other') AS label, COUNT(*) AS count
        FROM site_visitors
        GROUP BY label
        ORDER BY count DESC
        LIMIT 8
    ")->fetchAll() ?: [];

    return [
        'countries' => array_map(fn($row) => ['label' => (string)$row['label'], 'count' => (int)$row['count']], $countries),
        'devices' => array_map(fn($row) => ['label' => (string)$row['label'], 'count' => (int)$row['count']], $devices),
        'browsers' => array_map(fn($row) => ['label' => (string)$row['label'], 'count' => (int)$row['count']], $browsers),
    ];
}

function cached_visitor_distribution_summary(PDO $pdo, int $ttlSeconds = 180): array
{
    $cacheKey = 'visitor_distribution';
    $cached = cache_get($pdo, $cacheKey);
    if ($cached !== null) {
        return $cached;
    }
    $value = visitor_distribution_summary($pdo);
    cache_set($pdo, $cacheKey, $value, $ttlSeconds);
    return $value;
}

function visitor_engagement_estimates(PDO $pdo): array
{
    $overallStmt = $pdo->query("
        SELECT
            COUNT(*) AS total_visitors,
            SUM(CASE WHEN total_hits <= 1 THEN 1 ELSE 0 END) AS single_hit_visitors,
            AVG(total_hits) AS avg_hits,
            AVG(
                CASE
                    WHEN total_hits <= 1 THEN 30
                    ELSE GREATEST(30, TIMESTAMPDIFF(SECOND, first_seen, last_seen))
                END
            ) AS avg_session_seconds
        FROM site_visitors
    ");
    $overall = $overallStmt->fetch() ?: [];

    $todayStmt = $pdo->query("
        SELECT
            COUNT(*) AS total_today_visitors,
            SUM(CASE WHEN v.total_hits <= 1 THEN 1 ELSE 0 END) AS single_today_visitors
        FROM site_visitor_daily d
        INNER JOIN site_visitors v ON v.visitor_token = d.visitor_token
        WHERE d.visit_date = CURDATE()
    ");
    $today = $todayStmt->fetch() ?: [];

    $totalVisitors = (int)($overall['total_visitors'] ?? 0);
    $singleVisitors = (int)($overall['single_hit_visitors'] ?? 0);
    $todayTotal = (int)($today['total_today_visitors'] ?? 0);
    $todaySingle = (int)($today['single_today_visitors'] ?? 0);

    $bounceRate = $totalVisitors > 0 ? round(($singleVisitors / $totalVisitors) * 100, 2) : 0.0;
    $todayBounce = $todayTotal > 0 ? round(($todaySingle / $todayTotal) * 100, 2) : 0.0;
    $avgHits = round((float)($overall['avg_hits'] ?? 0), 2);
    $avgSessionMinutes = round(((float)($overall['avg_session_seconds'] ?? 0)) / 60, 2);

    return [
        'bounce_rate_overall' => $bounceRate,
        'bounce_rate_today' => $todayBounce,
        'avg_hits_per_visitor' => $avgHits,
        'avg_session_minutes' => $avgSessionMinutes,
    ];
}

function cached_visitor_engagement_estimates(PDO $pdo, int $ttlSeconds = 180): array
{
    $cacheKey = 'visitor_engagement';
    $cached = cache_get($pdo, $cacheKey);
    if ($cached !== null) {
        return $cached;
    }
    $value = visitor_engagement_estimates($pdo);
    cache_set($pdo, $cacheKey, $value, $ttlSeconds);
    return $value;
}
?>
