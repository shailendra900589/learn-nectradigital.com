<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';
require_once __DIR__ . '/includes/teach_figures.php';

function seo_meta(): array
{
    return [
        'title' => 'SEO Tutorial',
        'slug' => 'seo-tutorial',
        'description' => 'Search from the first query through the page, the index, links, and measurement, ending in one page you can explain.',
        'keywords' => 'seo tutorial, search intent, title tag, meta description, internal links, sitemap, canonical, search console',
    ];
}

function seo_doc(string $room, string $title, string $inner): string
{
    return '<!DOCTYPE html>' . "\n"
        . '<html><head><meta charset="utf-8"><title>' . htmlspecialchars($title, ENT_QUOTES) . '</title></head>' . "\n"
        . '<body data-room="' . htmlspecialchars($room, ENT_QUOTES) . '" style="margin:0;background:#f8fafc;color:#0f172a;font-family:sans-serif;">' . "\n"
        . '<main style="max-width:520px;margin:12px auto;background:#ffffff;padding:20px;border:1px solid #e2e8f0;border-radius:16px;">' . "\n"
        . $inner . "\n"
        . '</main></body></html>';
}

function seo_page(string $title, string $inner): string
{
    return seo_doc('seo-page', $title, $inner);
}

function seo_console(string $title, string $inner): string
{
    return seo_doc('seo-console', $title, $inner);
}

function seo_coverage(string $title, string $inner): string
{
    return seo_doc('seo-index', $title, $inner);
}

function seo_linkmap(string $title, string $inner): string
{
    return seo_doc('seo-links', $title, $inner);
}

function seo_result(string $headline, string $desc): string
{
    $inner = '<div style="font-size:13px;color:#059669;">milklist.example/shared-list</div>'
        . '<h1 style="margin:4px 0 0;font-size:20px;line-height:1.3;font-weight:500;color:#1a0dab;">' . htmlspecialchars($headline, ENT_QUOTES) . '</h1>'
        . '<p style="margin:6px 0 0;font-size:14px;line-height:1.5;color:#44403c;">' . htmlspecialchars($desc, ENT_QUOTES) . '</p>';
    return seo_doc('seo-serp', $headline, $inner);
}

function seo_chapters(): array
{
    require_once __DIR__ . '/seed_seo_a.php';
    require_once __DIR__ . '/seed_seo_b.php';
    require_once __DIR__ . '/seed_seo_c.php';
    require_once __DIR__ . '/seed_seo_d.php';
    $all = array_merge(seo_chapters_a(), seo_chapters_b(), seo_chapters_c(), seo_chapters_d());
    $project = [];
    $rest = [];
    foreach ($all as $ch) {
        if (str_starts_with($ch['title'], 'Project:')) {
            $project[] = $ch;
            continue;
        }
        $rest[] = $ch;
    }
    return array_merge($rest, $project);
}

if (PHP_SAPI === 'cli' && isset($_SERVER['argv'][0]) && realpath((string)$_SERVER['argv'][0]) === realpath(__FILE__)) {
    require_once __DIR__ . '/includes/db.php';
    $chapters = seo_chapters();
    if (count($chapters) !== 66) {
        fwrite(STDERR, 'expected 66 seo chapters, got ' . count($chapters) . "\n");
        exit(1);
    }
    foreach ($chapters as $ch) {
        $html = build_rich_chapter_html($ch);
        if (stripos($html, '<pre') !== false || stripos($html, 'language-') !== false) {
            fwrite(STDERR, "code block in {$ch['title']}\n");
            exit(1);
        }
    }
    track_seed($pdo, seo_meta(), $chapters);
    track_refresh($pdo, 'seo-tutorial', $chapters);
    track_place($pdo, 'seo-tutorial', array_map(static fn(array $ch): string => $ch['title'], $chapters));
    track_sections($pdo, 'seo-tutorial', [
        6 => 'The job',
        11 => 'How search works',
        14 => 'The query',
        17 => 'The result',
        25 => 'The page',
        33 => 'The site',
        37 => 'Trust',
        41 => 'Measurement',
        45 => 'Reading the report',
        53 => 'The machinery',
        60 => 'The content system',
        65 => 'Judgment',
        66 => 'The project',
    ]);
}
