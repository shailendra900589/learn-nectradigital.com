<?php
require_once 'includes/db.php';

// Tell the browser and Search Engines that this is an XML file, not a webpage
header("Content-Type: application/xml; charset=UTF-8");

// Your actual live domain
$base_url = "https://learn.nectradigital.com/";

// Start XML output
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// 1. Add Static Homepage
echo "  <url>\n";
echo "    <loc>" . $base_url . "index</loc>\n";
echo "    <changefreq>daily</changefreq>\n";
echo "    <priority>1.0</priority>\n";
echo "  </url>\n";

// 2. Fetch all Courses dynamically
$stmt = $pdo->query("SELECT id, slug, created_at FROM courses ORDER BY id DESC");
$courses = $stmt->fetchAll();

foreach ($courses as $course) {
    // Format the date for XML standards (ISO 8601)
    $date = date("Y-m-d\TH:i:s+00:00", strtotime($course['created_at']));
    
    echo "  <url>\n";
    // Construct the SEO-friendly URL
    echo "    <loc>" . htmlspecialchars($base_url . course_path($course['slug']), ENT_XML1, 'UTF-8') . "</loc>\n";
    echo "    <lastmod>" . $date . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.8</priority>\n";
    echo "  </url>\n";

    $stmtChapters = $pdo->prepare("SELECT id, chapter_name FROM chapters WHERE course_id = :course_id ORDER BY order_index ASC");
    $stmtChapters->execute(['course_id' => $course['id']]);
    foreach ($stmtChapters->fetchAll() as $chapter) {
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($base_url . chapter_path($course['slug'], (int)$chapter['id'], $chapter['chapter_name']), ENT_XML1, 'UTF-8') . "</loc>\n";
        echo "    <lastmod>" . $date . "</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
}

echo '</urlset>';
?>
