<?php
// Router script for PHP Built-in Development Server
$uri = rawurldecode((string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (preg_match('~(?:^|/)\.|\\\\|[\x00-\x1f]~', $uri)
    || preg_match('~^/(?:includes|sessions|vendor|node_modules)(?:/|$)|^/(?:seed[^/]*|router)(?:/|$)~i', $uri)
    || preg_match('~(?:^|/)(?:error_log|composer\.(?:json|lock)|package(?:-lock)?\.json|yarn\.lock|README\.md)$|\.(?:sql|bak|old|log|zip|tar|gz|ini|dist)$~i', $uri)) {
    http_response_code(403);
    exit;
}
if (str_starts_with($uri, '/assets/')) {
    $_GET['path'] = substr($uri, strlen('/assets/'));
    require __DIR__ . '/asset_guard.php';
    exit;
}
if ($uri === '/llms-full.txt') {
    http_response_code(410);
    exit;
}
if ($uri === '/llms.txt') {
    require __DIR__ . '/llms.php';
    exit;
}
$file = __DIR__ . $uri;

if ($uri !== '/' && is_file($file)) {
    return false;
}

if (preg_match('#^/(hi|bn|ta|te|mr|gu|pa)/?$#', $uri, $matches)) {
    $_GET['lang'] = $matches[1];
    require __DIR__ . '/index.php';
    exit;
}
if (preg_match('#^/(hi|bn|ta|te|mr|gu|pa)/course/([a-z0-9-]+)/([0-9]+)(?:-[a-z0-9-]+)?/?$#i', $uri, $matches)) {
    $_GET['lang'] = $matches[1];
    $_GET['slug'] = $matches[2];
    $_GET['chapter_id'] = (int)$matches[3];
    require __DIR__ . '/course.php';
    exit;
}
if (preg_match('#^/(hi|bn|ta|te|mr|gu|pa)/course/([a-z0-9-]+)/?$#i', $uri, $matches)) {
    $_GET['lang'] = $matches[1];
    $_GET['slug'] = $matches[2];
    require __DIR__ . '/course.php';
    exit;
}
if (preg_match('#^/(hi|bn|ta|te|mr|gu|pa)/profile/([a-f0-9]{32,64})/?$#i', $uri, $matches)) {
    $_GET['lang'] = $matches[1];
    $_GET['token'] = $matches[2];
    require __DIR__ . '/public_profile.php';
    exit;
}
if (preg_match('#^/(hi|bn|ta|te|mr|gu|pa)/(index|search|contact|privacy|terms|login|student|profile|billing|otp|logout)/?$#', $uri, $matches)) {
    $_GET['lang'] = $matches[1];
    require __DIR__ . '/' . $matches[2] . '.php';
    exit;
}

// 2. Rewrite course with chapter: /course/{slug}/{chapter_id}-{chapter_slug}
if (preg_match('#^/course/([a-z0-9-]+)/([0-9]+)(?:-[a-z0-9-]+)?/?$#i', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    $_GET['chapter_id'] = (int)$matches[2];
    require __DIR__ . '/course.php';
    exit;
}

// 3. Rewrite course root: /course/{slug}
if (preg_match('#^/course/([a-z0-9-]+)/?$#i', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    require __DIR__ . '/course.php';
    exit;
}

// 4. Public profile: /profile/{token}
if (preg_match('#^/profile/([a-f0-9]{32,64})/?$#i', $uri, $matches)) {
    $_GET['token'] = $matches[1];
    require __DIR__ . '/public_profile.php';
    exit;
}

// 5. XML routes
if ($uri === '/sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    exit;
}
if ($uri === '/feed.xml') {
    require __DIR__ . '/feed.php';
    exit;
}
if ($uri === '/opensearch.xml') {
    require __DIR__ . '/opensearch.xml.php';
    exit;
}

// 6. Direct extensionless PHP routes (e.g. /login -> /login.php, /admin/courses -> /admin/courses.php)
$phpFile = __DIR__ . rtrim($uri, '/') . '.php';
if (file_exists($phpFile)) {
    require $phpFile;
    exit;
}

// 7. Directory index fallback
if (is_dir($file) && file_exists(rtrim($file, '/') . '/index.php')) {
    require rtrim($file, '/') . '/index.php';
    exit;
}

if ($uri === '/') {
    require __DIR__ . '/index.php';
    exit;
}
http_response_code(404);
