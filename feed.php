<?php
require_once 'includes/db.php';

header('Content-Type: application/rss+xml; charset=UTF-8');

$site_url = rtrim(site_base_url(), '/');
$site_name = 'Learn.Nectra';
$site_description = 'Free programming tutorials, structured courses, and developer learning resources.';

$stmt = $pdo->query("
    SELECT
        c.title AS course_title,
        c.slug,
        c.description AS course_description,
        c.created_at,
        COUNT(DISTINCT sp.id) AS progress_hits,
        ch.id AS chapter_id,
        ch.chapter_name,
        ch.content,
        ch.order_index
    FROM courses c
    LEFT JOIN chapters ch ON ch.course_id = c.id AND ch.editorial_status = 'published'
    LEFT JOIN student_progress sp ON sp.course_id = c.id
    GROUP BY c.id, ch.id
    ORDER BY progress_hits DESC, c.created_at DESC, ch.order_index ASC
    LIMIT 50
");
$items = $stmt->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title><?php echo h($site_name); ?></title>
        <link><?php echo h($site_url); ?>/</link>
        <description><?php echo h($site_description); ?></description>
        <language>en-us</language>
        <lastBuildDate><?php echo date(DATE_RSS); ?></lastBuildDate>
        <atom:link href="<?php echo h($site_url); ?>/feed.xml" rel="self" type="application/rss+xml" />

        <?php foreach ($items as $item):
            $has_chapter = !empty($item['chapter_id']);
            $title = $has_chapter ? $item['chapter_name'] . ' | ' . $item['course_title'] : $item['course_title'];
            $description = $has_chapter ? seo_excerpt($item['content'], 220) : seo_excerpt($item['course_description'], 220);
            $link = $site_url . '/' . ($has_chapter ? chapter_path($item['slug'], (int)$item['chapter_id'], $item['chapter_name']) : course_path($item['slug']));
            $guid = $has_chapter ? 'chapter-' . (int)$item['chapter_id'] : 'course-' . rawurlencode($item['slug']);
            $recommendationTag = ((int)$item['progress_hits'] > 0) ? 'Trending recommendation' : 'Fresh recommendation';
            $encodedBody = str_replace(']]>', ']]]]><![CDATA[>', plain_text($description . ' • ' . $recommendationTag));
        ?>
            <item>
                <title><?php echo h($title); ?></title>
                <link><?php echo h($link); ?></link>
                <guid isPermaLink="false"><?php echo h($guid); ?></guid>
                <description><?php echo h($description); ?></description>
                <content:encoded><![CDATA[<?php echo $encodedBody; ?>]]></content:encoded>
                <pubDate><?php echo date(DATE_RSS, strtotime($item['created_at'])); ?></pubDate>
                <category><?php echo h($item['course_title']); ?></category>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>
