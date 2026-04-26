<?php
require_once __DIR__ . '/security.php';

function plain_text(string $value): string
{
    return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
}

function seo_excerpt(string $value, int $max = 155): string
{
    $text = plain_text($value);
    if (strlen($text) <= $max) {
        return $text;
    }

    $cut = substr($text, 0, $max);
    $space = strrpos($cut, ' ');
    return trim(substr($cut, 0, $space ?: $max), " \t\n\r\0\x0B.,;:") . '.';
}

function seo_limit(string $value, int $max): string
{
    $text = plain_text($value);
    if (strlen($text) <= $max) {
        return $text;
    }

    $cut = substr($text, 0, $max);
    $space = strrpos($cut, ' ');
    return trim(substr($cut, 0, $space ?: $max), " \t\n\r\0\x0B.,;:-");
}

function seo_title_text(string $title, string $brand = 'Nectra Digital', int $max = 59): string
{
    $title = trim(plain_text($title));
    $suffix = ' | ' . $brand;

    if (stripos($title, $brand) !== false) {
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
    return rtrim(app_base_path(), '/') . '/' . ltrim($path, '/');
}

function site_base_url(): string
{
    $configured = getenv('SITE_URL');
    if ($configured) {
        return rtrim($configured, '/') . '/';
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'learn.nectradigital.com';
    return $scheme . '://' . $host . app_base_path();
}

function current_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'learn.nectradigital.com';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return $scheme . '://' . $host . $uri;
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
    $content = normalize_chapter_markdown($content);

    $looksMarkdown = preg_match('/(^|\s)(#{2,4}\s|\*\*|\[IMAGE:|\d+\.\s+\*\*)/', plain_text($content));
    if (preg_match('/<(p|h[1-6]|ul|ol|pre|table|blockquote|img|iframe)\b/i', $content) && !$looksMarkdown) {
        $content = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $content);
        $content = preg_replace('/\son[a-z]+\s*=\s*("|\').*?\1/is', '', $content);
        return '<div class="chapter-body">' . $content . '</div>';
    }

    if ($looksMarkdown) {
        $content = normalize_chapter_markdown(strip_tags($content));
    }

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

    $slug = slugify($label, 'ad');
    return '<aside class="ad-slot ad-slot-' . h($slug) . ' my-8" aria-label="' . h($label) . '"><span>Sponsored</span><div class="ad-slot-frame">' . $adCode . '</div></aside>';
}

function schema_script(array $data): string
{
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}
?>
