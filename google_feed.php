<?php
require_once 'includes/db.php';

header('Content-Type: application/rss+xml; charset=UTF-8');

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
    LIMIT 30
");
$courses = $stmt->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Learn.Nectra Google Search Feed</title>
        <link><?php echo h($siteUrl); ?>/</link>
        <description>Recommended tutorial feed optimized for search engines.</description>
        <language>en-us</language>
        <atom:link href="<?php echo h($siteUrl); ?>/google-feed.xml" rel="self" type="application/rss+xml" />
        <lastBuildDate><?php echo date(DATE_RSS); ?></lastBuildDate>

        <?php foreach ($courses as $course): ?>
            <item>
                <title><?php echo h($course['title']); ?> (<?php echo (int)$course['chapter_count']; ?> lessons)</title>
                <link><?php echo h($siteUrl . '/' . course_path($course['slug'])); ?></link>
                <guid isPermaLink="true"><?php echo h($siteUrl . '/' . course_path($course['slug'])); ?></guid>
                <description><?php echo h(seo_excerpt($course['description'], 200)); ?></description>
                <category>Recommended Tutorial</category>
                <pubDate><?php echo date(DATE_RSS, strtotime($course['created_at'])); ?></pubDate>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>
