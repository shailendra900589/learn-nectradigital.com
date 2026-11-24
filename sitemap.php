<?php
require_once 'includes/db.php';
require_once 'includes/content.php';

header("Content-Type: application/xml; charset=UTF-8");

$base_url = rtrim(site_base_url(), '/');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

$emitSitemapUrl = static function (string $relative, string $freq, string $prio, string $lastmod = ''): void {
    $urls = page_language_urls($relative);
    foreach ($urls as $loc) {
        echo "  <url>\n";
        echo '    <loc>' . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n";
        if ($lastmod !== '') {
            echo '    <lastmod>' . $lastmod . "</lastmod>\n";
        }
        echo '    <changefreq>' . $freq . "</changefreq>\n";
        echo '    <priority>' . $prio . "</priority>\n";
        foreach ($urls as $code => $alt) {
            echo '    <xhtml:link rel="alternate" hreflang="' . supported_languages()[$code]['html'] . '" href="' . htmlspecialchars($alt, ENT_XML1, 'UTF-8') . "\"/>\n";
        }
        echo '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($urls['en'], ENT_XML1, 'UTF-8') . "\"/>\n";
        echo "  </url>\n";
    }
};

// 1. Static Core Landing Pages
$staticPages = [
    '' => ['freq' => 'daily', 'prio' => '1.0'],
    'search' => ['freq' => 'weekly', 'prio' => '0.8'],
    'contact' => ['freq' => 'monthly', 'prio' => '0.5'],
    'privacy' => ['freq' => 'yearly', 'prio' => '0.3'],
    'terms' => ['freq' => 'yearly', 'prio' => '0.3'],
];

foreach ($staticPages as $path => $meta) {
    $emitSitemapUrl($path, $meta['freq'], $meta['prio']);
}

// 2. Fetch all Courses & Chapters dynamically
$stmt = $pdo->query("SELECT id, title, slug, created_at FROM courses ORDER BY id ASC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($courses as $course) {
    $courseDate = date("Y-m-d\TH:i:s+00:00", !empty($course['created_at']) ? strtotime($course['created_at']) : time());
    $stmtChapters = $pdo->prepare("SELECT id, chapter_name FROM chapters WHERE course_id = :course_id ORDER BY order_index ASC");
    $stmtChapters->execute(['course_id' => $course['id']]);
    foreach ($stmtChapters->fetchAll(PDO::FETCH_ASSOC) as $chapter) {
        $emitSitemapUrl(chapter_path($course['slug'], (int)$chapter['id'], $chapter['chapter_name']), 'weekly', '0.8', $courseDate);
    }
}

echo '</urlset>';
