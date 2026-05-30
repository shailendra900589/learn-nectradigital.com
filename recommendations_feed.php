<?php
require_once 'includes/db.php';

header('Content-Type: application/feed+json; charset=UTF-8');

$siteUrl = rtrim(site_base_url(), '/');

$stmt = $pdo->query("
    SELECT
        c.title,
        c.slug,
        c.description,
        c.created_at,
        COUNT(DISTINCT ch.id) AS chapter_count,
        COUNT(DISTINCT sp.id) AS popularity
    FROM courses c
    LEFT JOIN chapters ch ON ch.course_id = c.id
    LEFT JOIN student_progress sp ON sp.course_id = c.id
    GROUP BY c.id
    ORDER BY popularity DESC, c.created_at DESC
    LIMIT 40
");
$courses = $stmt->fetchAll();

$items = [];
foreach ($courses as $course) {
    $url = $siteUrl . '/' . course_path($course['slug']);
    $items[] = [
        'id' => $url,
        'url' => $url,
        'title' => $course['title'],
        'content_text' => seo_excerpt($course['description'], 240),
        'summary' => 'Recommended for search engines and aggregators',
        'date_published' => date('c', strtotime($course['created_at'])),
        'tags' => ['tutorial', 'programming', ((int)$course['popularity'] > 0 ? 'trending' : 'new')],
        'attachments' => [
            [
                'url' => $url,
                'mime_type' => 'text/html',
                'title' => (int)$course['chapter_count'] . ' lessons',
            ],
        ],
    ];
}

echo json_encode([
    'version' => 'https://jsonfeed.org/version/1.1',
    'title' => 'Learn.Nectra Recommendations Feed',
    'home_page_url' => $siteUrl . '/',
    'feed_url' => $siteUrl . '/recommendations-feed.json',
    'description' => 'Auto recommendations feed for SEO, GEO, and AEO distribution.',
    'language' => 'en',
    'items' => $items,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
