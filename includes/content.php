<?php
require_once __DIR__ . '/security.php';

function plain_text(string $value): string
{
    return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
}

function seo_length(string $text): int
{
    return function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
}

function seo_piece(string $text, int $start, int $length): string
{
    if ($length < 0) {
        $length = 0;
    }
    return function_exists('mb_substr') ? (string)mb_substr($text, $start, $length, 'UTF-8') : substr($text, $start, $length);
}

function seo_last_space(string $text)
{
    if (function_exists('mb_strrpos')) {
        $pos = mb_strrpos($text, ' ', 0, 'UTF-8');
        return $pos === false ? false : $pos;
    }
    return strrpos($text, ' ');
}

function seo_excerpt(string $value, int $max = 155): string
{
    $text = plain_text($value);
    if (seo_length($text) <= $max) {
        return $text;
    }

    $cut = seo_piece($text, 0, $max);
    $space = seo_last_space($cut);
    return trim(seo_piece($cut, 0, $space === false ? $max : $space), " \t\n\r\0\x0B.,;:") . '.';
}

function seo_limit(string $value, int $max): string
{
    $text = plain_text($value);
    if (seo_length($text) <= $max) {
        return $text;
    }

    $cut = seo_piece($text, 0, $max);
    $space = seo_last_space($cut);
    return trim(seo_piece($cut, 0, $space === false ? $max : $space), " \t\n\r\0\x0B.,;:|-");
}

function lesson_seo_title(string $chapter, string $course): string
{
    $brand = ' | Nectra Digital';
    $tail = ' - ' . trim($course) . $brand;
    $full = trim($chapter) . $tail;
    $max = 68;
    if (seo_length($full) <= $max) {
        return $full;
    }
    $room = $max - seo_length($tail);
    if ($room < 24) {
        $tail = ' - ' . seo_limit($course, 24) . $brand;
        $room = $max - seo_length($tail);
    }
    if ($room < 16) {
        $room = 16;
    }
    return seo_limit($chapter, $room) . $tail;
}

function seo_title_text(string $title, string $brand = 'Nectra Digital', int $max = 59): string
{
    $title = trim(plain_text($title));
    $suffix = ' | ' . $brand;

    if (stripos($title, $brand) !== false) {
        $title = preg_replace('/\|\s*\|/', '|', $title) ?? $title;
        return seo_limit($title, $max);
    }

    $allowed = $max - strlen($suffix);
    if ($allowed < 20) {
        return seo_limit($brand, $max);
    }

    return seo_limit($title, $allowed) . $suffix;
}

function seo_description_text(string $description, string $brand = 'Nectra Digital', int $max = 149): string
{
    $description = trim(plain_text($description));

    if (stripos($description, $brand) !== false) {
        return seo_limit($description, $max);
    }

    $suffix = ' Learn with ' . $brand . '.';
    $allowed = $max - strlen($suffix);
    if ($allowed < 50) {
        return seo_limit($brand, $max);
    }

    return rtrim(seo_limit($description, $allowed), " \t\n\r\0\x0B.,;:") . '.' . $suffix;
}

function slugify(string $value, string $fallback = 'item'): string
{
    $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    $value = trim(preg_replace('/-+/', '-', $value), '-');
    return $value !== '' ? $value : $fallback;
}

function course_path(string $slug): string
{
    return 'course/' . rawurlencode(slugify($slug, 'course'));
}

function chapter_path(string $courseSlug, int $chapterId, string $chapterName = ''): string
{
    $chapterSlug = slugify($chapterName, 'chapter');
    return course_path($courseSlug) . '/' . $chapterId . '-' . rawurlencode($chapterSlug);
}

function absolute_url(string $path): string
{
    return rtrim(site_base_url(), '/') . '/' . ltrim($path, '/');
}

function app_base_path(): string
{
    $configured = getenv('APP_BASE_PATH');
    if ($configured !== false && $configured !== '') {
        $configured = trim($configured, '/');
        return $configured === '' ? '/' : '/' . $configured . '/';
    }

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    if ($dir === '' || $dir === '.') {
        return '/';
    }

    if (preg_match('#/admin$#', $dir)) {
        $dir = rtrim(dirname($dir), '/');
    }

    return ($dir === '' || $dir === '.') ? '/' : $dir . '/';
}

function app_path(string $path = ''): string
{
    $path = ltrim($path, '/');
    $query = '';
    if (($queryPos = strpos($path, '?')) !== false) {
        $query = substr($path, $queryPos);
        $path = substr($path, 0, $queryPos);
    }
    $prefix = function_exists('public_lang_prefix') ? public_lang_prefix() : '';
    return rtrim(app_base_path(), '/') . '/' . $prefix . $path . $query;
}

function trusted_request_host(): string
{
    $configured = getenv('SITE_URL') ?: '';
    $allowed = ['learn.nectradigital.com', 'learning.nectradigital.com', 'www.learn.nectradigital.com', 'www.learning.nectradigital.com'];
    $configuredHost = '';
    if ($configured !== '') {
        $parsed = parse_url($configured);
        $configuredHost = strtolower((string)($parsed['host'] ?? ''));
        if ($configuredHost !== '') {
            $allowed[] = $configuredHost;
        }
    }
    $localClient = in_array((string)($_SERVER['REMOTE_ADDR'] ?? ''), ['127.0.0.1', '::1'], true);
    if ($localClient) {
        $allowed[] = 'localhost';
        $allowed[] = '127.0.0.1';
        $allowed[] = '[::1]';
    }

    $raw = strtolower(trim((string)($_SERVER['HTTP_HOST'] ?? '')));
    $hostOnly = (string)preg_replace('/:\d+$/', '', $raw);
    if ($raw !== '' && preg_match('/^[a-z0-9.\-\[\]]+(?::\d+)?$/', $raw) && in_array($hostOnly, $allowed, true)) {
        return $raw;
    }
    if ($configuredHost !== '') {
        return $configuredHost . (!empty($parsed['port']) ? ':' . $parsed['port'] : '');
    }
    return 'learn.nectradigital.com';
}

function site_base_url(): string
{
    $configured = getenv('SITE_URL');
    if ($configured) {
        return rtrim($configured, '/') . '/';
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . trusted_request_host() . app_base_path();
}

function current_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    if (!is_string($uri) || $uri === '' || $uri[0] !== '/' || str_contains($uri, '\\') || str_contains($uri, "\n") || str_contains($uri, "\r")) {
        $uri = '/';
    }
    return $scheme . '://' . trusted_request_host() . $uri;
}

function render_inline_markdown(string $text): string
{
    $text = h($text);
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/(?<!\*)\*(?!\s)(.+?)(?<!\s)\*(?!\*)/s', '<em>$1</em>', $text);
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    return $text;
}

function normalize_chapter_markdown(string $content): string
{
    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $content = str_replace(["\r\n", "\r"], "\n", $content);
    $content = preg_replace('/\s+(#{2,4}\s+)/', "\n\n$1", $content);
    $content = preg_replace('/\s+(\d+\.\s+\*\*)/', "\n$1", $content);
    $content = preg_replace('/\s+(\*\s+\*\*)/', "\n$1", $content);
    $content = preg_replace('/\s+(\[IMAGE:\s*.+?\])/', "\n\n$1\n\n", $content);
    $content = preg_replace('/>\s*\*\*\[INSERT IMAGE HERE:\s*(.+?)\]\*\*/i', "\n\n[IMAGE: $1]\n\n", $content);
    $content = preg_replace('/\*\*\[INSERT IMAGE HERE:\s*(.+?)\]\*\*/i', "\n\n[IMAGE: $1]\n\n", $content);
    return trim($content);
}

function render_chapter_content(string $content): string
{
    $hasStructuredHtml = (bool)preg_match('/<(p|h[1-6]|ul|ol|pre|table|blockquote|img|iframe|div|section)\b/i', $content);

    if ($hasStructuredHtml) {
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $preserved = [];
        $content = preg_replace_callback('/<pre\b[^>]*>.*?<\/pre>/is', static function (array $matches) use (&$preserved): string {
            $token = '%%NECTRA_PRE_' . count($preserved) . '%%';
            $preserved[$token] = $matches[0];
            return $token;
        }, $content) ?? $content;

        $content = preg_replace('#<(script|iframe|object|embed|base|meta)\b[^>]*>.*?</\1>#is', '', $content) ?? $content;
        $content = preg_replace('#<(script|iframe|object|embed|base|meta)\b[^>]*\/?>#is', '', $content) ?? $content;
        $content = preg_replace_callback('/\s(?:href|src|xlink:href|action|formaction)\s*=\s*("|\')(.*?)\1/is', static function (array $matches): string {
            $url = trim(html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $dangerousScheme = preg_match('#^(?:javascript|vbscript):#i', $url) === 1;
            $dangerousData = preg_match('#^data:#i', $url) === 1 && preg_match('#^data:image/(?:png|jpe?g|gif|webp)[;,]#i', $url) !== 1;
            return ($dangerousScheme || $dangerousData) ? '' : $matches[0];
        }, $content) ?? $content;

        foreach ($preserved as $token => $pre) {
            $safePre = preg_replace_callback('/(<pre[^>]*>)(?:\s*(<code[^>]*>))?(.*?)(?:(<\/code>\s*))?(<\/pre>)/is', static function (array $matches): string {
                $openPre = $matches[1];
                $openCode = $matches[2] ?? '';
                $code = $matches[3];
                $closeCode = $matches[4] ?? '';
                $closePre = $matches[5];
                $decoded = html_entity_decode($code, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $cleanEscaped = htmlspecialchars($decoded, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                return $openPre . ($openCode ?: '') . $cleanEscaped . ($closeCode ?: '') . $closePre;
            }, $pre) ?? $pre;
            $content = str_replace($token, $safePre, $content);
        }

        return '<div class="chapter-body">' . $content . '</div>';
    }

    $content = normalize_chapter_markdown($content);

    $lines = preg_split('/\n+/', $content);
    $html = '';
    $listOpen = null;
    $paragraph = [];

    $flushParagraph = function () use (&$html, &$paragraph): void {
        if (!$paragraph) {
            return;
        }
        $html .= '<p>' . render_inline_markdown(trim(implode(' ', $paragraph))) . '</p>';
        $paragraph = [];
    };

    $closeList = function () use (&$html, &$listOpen): void {
        if ($listOpen) {
            $html .= '</' . $listOpen . '>';
            $listOpen = null;
        }
    };

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            $flushParagraph();
            $closeList();
            continue;
        }

        if (preg_match('/^\[IMAGE:\s*(.+?)\]$/i', $line, $match)) {
            $flushParagraph();
            $closeList();
            $html .= '<figure class="chapter-image-placeholder"><div>' . render_inline_markdown($match[1]) . '</div><figcaption>Suggested visual for this section</figcaption></figure>';
            continue;
        }

        if (preg_match('/^(#{2,4})\s+(.+)$/', $line, $match)) {
            $flushParagraph();
            $closeList();
            $level = min(4, strlen($match[1]) + 1);
            $heading = trim($match[2]);
            $heading = preg_replace('/\s+(?=\*\*)/', "\n", $heading, 1);
            [$headingText, $rest] = array_pad(explode("\n", $heading, 2), 2, '');
            if ($rest === '' && strlen($headingText) > 90 && preg_match('/^(.{20,90}?)(\s+(?:To|When|Here|In|This)\s+.+)$/', $headingText, $split)) {
                $headingText = trim($split[1]);
                $rest = trim($split[2]);
            }
            $html .= '<h' . $level . '>' . render_inline_markdown($headingText) . '</h' . $level . '>';
            if (trim($rest) !== '') {
                $paragraph[] = trim($rest);
            }
            continue;
        }

        if (preg_match('/^\d+\.\s+(.+)$/', $line, $match)) {
            $flushParagraph();
            if ($listOpen !== 'ol') {
                $closeList();
                $html .= '<ol>';
                $listOpen = 'ol';
            }
            $html .= '<li>' . render_inline_markdown($match[1]) . '</li>';
            continue;
        }

        if (preg_match('/^[-*]\s+(.+)$/', $line, $match)) {
            $flushParagraph();
            if ($listOpen !== 'ul') {
                $closeList();
                $html .= '<ul>';
                $listOpen = 'ul';
            }
            $html .= '<li>' . render_inline_markdown($match[1]) . '</li>';
            continue;
        }

        if (str_starts_with($line, '>')) {
            $flushParagraph();
            $closeList();
            $html .= '<blockquote>' . render_inline_markdown(trim(substr($line, 1))) . '</blockquote>';
            continue;
        }

        $paragraph[] = $line;
    }

    $flushParagraph();
    $closeList();

    return '<div class="chapter-body">' . $html . '</div>';
}

function render_ad_slot(?string $adCode, string $label = 'Advertisement'): string
{
    if (!$adCode) {
        return '';
    }

    $adCode = preg_replace_callback(
        '~(\bsrc=["\'])(assets/uploads/ads/[a-f0-9]{24}\.(?:jpg|png|gif|webp))(["\'])~i',
        fn(array $match): string => $match[1] . h(protected_asset_url($match[2])) . $match[3],
        $adCode
    ) ?? $adCode;

    $slug = slugify($label, 'ad');
    return '<aside class="ad-slot ad-slot-' . h($slug) . ' my-8" aria-label="' . h($label) . '"><span>Sponsored</span><div class="ad-slot-frame">' . $adCode . '</div></aside>';
}

function active_content_ad_groups(PDO $pdo): array
{
    if (!should_show_ads($pdo)) {
        return [];
    }

    $groups = [];
    try {
        foreach ($pdo->query("SELECT location, ad_code FROM ads WHERE is_active = 1 ORDER BY id") as $ad) {
            if (trim((string)$ad['ad_code']) !== '') {
                $groups[$ad['location']][] = $ad['ad_code'];
            }
        }
    } catch (PDOException $error) {
        error_log('Advertisement lookup failed: ' . $error->getMessage());
    }
    return $groups;
}

function render_ad_group(array $codes, string $label): string
{
    return implode('', array_map(fn(string $code): string => render_ad_slot($code, $label), $codes));
}

function insert_lesson_ads(string $html, array $groups): string
{
    if (!$groups) {
        return $html;
    }

    $labels = content_ad_locations();
    $positions = [
        'inline_after_first' => 1, 'text_link' => 2, 'sponsored_note' => 3,
        'resource_box' => 4, 'sitewide' => 5, 'inline_mid' => 5, 'cta_card' => 6,
        'code_break' => 7, 'quiz_prompt' => 8,
    ];
    $parts = preg_split('/(<\/p\s*>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    $paragraphs = intdiv(count($parts) - 1, 2);
    $afterParagraph = [];
    $afterLesson = '';
    foreach ($positions as $location => $number) {
        $ad = render_ad_group($groups[$location] ?? [], $labels[$location]);
        if ($paragraphs >= $number) {
            $afterParagraph[$number] = ($afterParagraph[$number] ?? '') . $ad;
        } else {
            $afterLesson .= $ad;
        }
    }
    foreach ($afterParagraph as $number => $markup) {
        $parts[$number * 2 - 1] .= $markup;
    }

    return implode('', $parts) . $afterLesson
        . render_ad_group($groups['inline_before_summary'] ?? [], $labels['inline_before_summary'])
        . render_ad_group($groups['bottom_recommendation'] ?? [], $labels['bottom_recommendation']);
}

function schema_script(array $data): string
{
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

function compact_html_document(string $html): string
{
    if (!str_contains(substr($html, 0, 256), '<!DOCTYPE html>')) {
        return $html;
    }

    $preserved = [];
    $html = preg_replace_callback(
        '#<(pre|textarea|script|style)\b[^>]*>.*?</\1>#is',
        function (array $m) use (&$preserved): string {
            $key = "\x00NECTRA_BLOCK_" . count($preserved) . "\x00";
            $preserved[$key] = $m[0];
            return $key;
        },
        $html
    ) ?? $html;

    // Strip standard HTML comments (not IE/conditional comments)
    $html = preg_replace('/<!--(?!\[if|\s*<!).*?-->/s', '', $html) ?? $html;

    // Collapse multi-line whitespace and indentation into a single clean space
    $html = preg_replace('/[ \t]*[\r\n]+[ \t\r\n]*/', ' ', $html) ?? $html;
    $html = preg_replace('/ {2,}/', ' ', $html) ?? $html;
    $html = preg_replace('/>\s+(<\/?(?:html|head|meta|title|base|link|body|header|nav|aside|main|section|footer|div|ul|li)\b)/i', '>$1', $html) ?? $html;

    // Keep clean, readable structural line breaks (~15-25 lines total in View-Source)
    $html = preg_replace('/(<!DOCTYPE html>|<html\b[^>]*>|<head>|<\/head>|<body\b[^>]*>|<header\b[^>]*>|<\/header>|<main\b[^>]*>|<\/main>|<footer\b[^>]*>|<\/footer>|<\/body>)/i', "$1\n", $html) ?? $html;

    if ($preserved) {
        $html = strtr($html, $preserved);
    }

    return trim($html) . "\n";
}

function start_clean_html_buffer(): void
{
    static $started = false;
    if ($started || PHP_SAPI === 'cli') {
        return;
    }
    $started = true;
    ob_start('compact_html_document');
}
?>
