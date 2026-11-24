<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/security.php';

function deny_asset_access(): never
{
    http_response_code(403);
    header_remove('Expires');
    header_remove('Pragma');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Vary: Sec-Fetch-Dest, Sec-Fetch-Mode, Sec-Fetch-Site, Referer, Accept');
    header('X-Robots-Tag: noindex, nofollow, noarchive');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Content-Type: text/html; charset=UTF-8');

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><meta name="robots" content="noindex,nofollow"><title>403 — Not Allowed</title><style>*{box-sizing:border-box;margin:0;padding:0}body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0f172a;color:#f8fafc;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;padding:24px}.card{max-width:460px;width:100%;background:#1e293b;border:1px solid #334155;border-radius:16px;padding:36px 32px;text-align:center;box-shadow:0 20px 50px rgba(0,0,0,.45)}.badge{display:inline-flex;align-items:center;gap:6px;background:rgba(239,68,68,.12);color:#f87171;border:1px solid rgba(239,68,68,.3);font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:5px 12px;border-radius:999px;margin-bottom:16px}h1{font-size:28px;font-weight:800;letter-spacing:-.02em;margin-bottom:10px}p{color:#94a3b8;font-size:14px;line-height:1.6}</style></head><body><div class="card"><div class="badge">403 Forbidden</div><h1>Access Not Allowed</h1><p>Direct access to protected application assets and directories is strictly prohibited.</p></div></body></html>';
    exit;
}

function normalize_host_port(string $host, string $scheme = ''): string
{
    $host = strtolower(trim($host));
    if ($scheme === 'http' && str_ends_with($host, ':80')) {
        return substr($host, 0, -3);
    }
    if ($scheme === 'https' && str_ends_with($host, ':443')) {
        return substr($host, 0, -4);
    }
    return $host;
}

$rawPath = trim((string)($_GET['path'] ?? ''), "/\\ \t\n\r\0\x0B");
if ($rawPath === '' || str_contains($rawPath, '..') || str_contains($rawPath, "\0")) {
    deny_asset_access();
}

foreach (explode('/', str_replace('\\', '/', $rawPath)) as $segment) {
    if ($segment === '' || $segment[0] === '.') {
        deny_asset_access();
    }
}

$assetsRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'assets');
if ($assetsRoot === false) {
    deny_asset_access();
}

$candidate = $assetsRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rawPath);
$targetFile = realpath($candidate);
if ($targetFile === false || !is_file($targetFile)) {
    deny_asset_access();
}

$rootPrefix = rtrim($assetsRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
if (strncasecmp($targetFile, $rootPrefix, strlen($rootPrefix)) !== 0) {
    deny_asset_access();
}

$relAssetPath = 'assets/' . str_replace('\\', '/', substr($targetFile, strlen($rootPrefix)));
$ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
$isChapterDownload = str_starts_with($relAssetPath, 'assets/uploads/chapter-downloads/');

$mimeMap = [
    'js'    => 'application/javascript; charset=UTF-8',
    'mjs'   => 'application/javascript; charset=UTF-8',
    'css'   => 'text/css; charset=UTF-8',
    'png'   => 'image/png',
    'jpg'   => 'image/jpeg',
    'jpeg'  => 'image/jpeg',
    'gif'   => 'image/gif',
    'webp'  => 'image/webp',
    'svg'   => 'image/svg+xml',
    'ico'   => 'image/x-icon',
    'avif'  => 'image/avif',
    'woff'  => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf'   => 'font/ttf',
    'otf'   => 'font/otf',
    'eot'   => 'application/vnd.ms-fontobject',
];

$downloadMimeMap = [
    'pdf'  => 'application/pdf',
    'zip'  => 'application/zip',
    'txt'  => 'text/plain; charset=UTF-8',
    'json' => 'application/json; charset=UTF-8',
    'csv'  => 'text/csv; charset=UTF-8',
    'md'   => 'text/markdown; charset=UTF-8',
    'sql'  => 'text/plain; charset=UTF-8',
    'py'   => 'text/plain; charset=UTF-8',
    'html' => 'text/plain; charset=UTF-8',
];

$contentType = $mimeMap[$ext] ?? ($isChapterDownload ? ($downloadMimeMap[$ext] ?? null) : null);
if ($contentType === null) {
    deny_asset_access();
}

// 1. Strict same-host Referer check (blocks view-source clicks, address-bar URLs, bookmarks, external hotlinks)
$referer = trim((string)($_SERVER['HTTP_REFERER'] ?? ''));
if ($referer === '') {
    deny_asset_access();
}

$refParts = parse_url($referer);
if (!is_array($refParts) || empty($refParts['host'])) {
    deny_asset_access();
}

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
$curScheme = $isHttps ? 'https' : 'http';
$curHost = normalize_host_port((string)($_SERVER['HTTP_HOST'] ?? ''), $curScheme);
$refScheme = strtolower((string)($refParts['scheme'] ?? $curScheme));
$refHostRaw = (string)$refParts['host'] . (isset($refParts['port']) ? ':' . $refParts['port'] : '');
$refHost = normalize_host_port($refHostRaw, $refScheme);

if ($curHost === '' || $refHost !== $curHost) {
    deny_asset_access();
}

// 2. W3C Fetch Metadata enforcement (blocks top-level navigations, iframes, embeds, DevTools fetch())
$fetchSite = strtolower(trim((string)($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '')));
$fetchMode = strtolower(trim((string)($_SERVER['HTTP_SEC_FETCH_MODE'] ?? '')));
$fetchDest = strtolower(trim((string)($_SERVER['HTTP_SEC_FETCH_DEST'] ?? '')));
$acceptHeader = strtolower(trim((string)($_SERVER['HTTP_ACCEPT'] ?? '')));

if ($fetchSite !== '' && $fetchSite !== 'same-origin') {
    deny_asset_access();
}

if ($isChapterDownload) {
    // Chapter downloads must come from a course page click with a valid signed token
    $refPath = (string)($refParts['path'] ?? '');
    if (!str_contains($refPath, 'course')) {
        deny_asset_access();
    }
} else {
    // Never allow top-level browser navigation or frame embedding for assets
    if ($fetchMode === 'navigate' || $fetchMode === 'nested-navigate') {
        deny_asset_access();
    }

    if (in_array($fetchDest, ['document', 'iframe', 'frame', 'fencedframe', 'object', 'embed'], true)) {
        deny_asset_access();
    }

    if ($fetchDest !== '') {
        if (($ext === 'js' || $ext === 'mjs') && $fetchDest !== 'script') {
            deny_asset_access();
        }
        if ($ext === 'css' && $fetchDest !== 'style') {
            deny_asset_access();
        }
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'ico', 'avif'], true) && $fetchDest !== 'image') {
            deny_asset_access();
        }
        if (in_array($ext, ['woff', 'woff2', 'ttf', 'otf', 'eot'], true) && !in_array($fetchDest, ['font', 'style', 'empty'], true)) {
            deny_asset_access();
        }
    } else {
        // Fallback when Sec-Fetch-* is absent (e.g. non-HTTPS LAN or CLI HTTP clients):
        // Block HTML document navigations and require an active browser session established by a page visit.
        if (in_array($ext, ['js', 'mjs', 'css'], true) && str_contains($acceptHeader, 'text/html')) {
            deny_asset_access();
        }
        $localSessionPath = __DIR__ . '/sessions';
        if (is_dir($localSessionPath)) {
            session_save_path($localSessionPath);
        }
        @session_start(['read_and_close' => true]);
        if (empty($_SESSION['asset_session_ok'])) {
            deny_asset_access();
        }
    }
}

// 3. Cryptographic HMAC signature enforcement on JS, CSS, and chapter downloads
$mtime = (int)filemtime($targetFile);
if ($ext === 'js' || $ext === 'mjs' || $ext === 'css' || $isChapterDownload) {
    $sig = (string)($_GET['_s'] ?? '');
    if (!verify_asset_signature($relAssetPath, $mtime, $sig)) {
        deny_asset_access();
    }
}

if ($isChapterDownload || str_starts_with($relAssetPath, 'assets/uploads/students/')) {
    require_once __DIR__ . '/includes/db.php';
    secure_session_start();
    $studentId = (int)($_SESSION['student_id'] ?? 0);
    if ($isChapterDownload) {
        $stmt = $pdo->prepare('SELECT MIN(is_premium) FROM chapters WHERE download_file = ?');
        $stmt->execute([$relAssetPath]);
        $premium = $stmt->fetchColumn();
        if ($premium === null || $premium === false
            || ($studentId > 0 && !student_can_view_content($pdo, $studentId))
            || ((int)$premium === 1 && ($studentId <= 0 || !active_subscription($pdo, $studentId)))) {
            deny_asset_access();
        }
    } else {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE profile_photo = ?
            AND ((profile_public = 1 AND account_status = 'active') OR id = ?) LIMIT 1");
        $stmt->execute([$relAssetPath, $studentId]);
        if (!$stmt->fetchColumn() && empty($_SESSION['admin_logged_in'])) {
            deny_asset_access();
        }
    }
    session_write_close();
}

// 4. Deliver protected asset with cache-partitioning Vary + mandatory revalidation so view-source clicks never hit disk cache
$size = (int)filesize($targetFile);
$etag = '"' . substr(sha1($relAssetPath . '|' . $mtime . '|' . $size), 0, 24) . '"';

header_remove('Expires');
header_remove('Pragma');
header('Cache-Control: private, no-cache, must-revalidate');
header('Vary: Sec-Fetch-Dest, Sec-Fetch-Mode, Sec-Fetch-Site, Referer, Accept');
header('ETag: ' . $etag);
header('X-Content-Type-Options: nosniff');
header('X-Robots-Tag: noindex, nofollow');

$ifNoneMatch = trim((string)($_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));
if ($ifNoneMatch !== '' && $ifNoneMatch === $etag && !$isChapterDownload) {
    http_response_code(304);
    exit;
}

header('Content-Type: ' . $contentType);
if ($isChapterDownload) {
    header('Content-Disposition: attachment; filename="' . rawurlencode(basename($targetFile)) . '"');
}
header('Content-Length: ' . $size);
readfile($targetFile);
exit;
