<?php
require_once 'includes/db.php';
require_once 'includes/content.php';

header("Content-Type: text/plain; charset=UTF-8");

$isFull = isset($_GET['full']) && $_GET['full'] == '1';
$baseUrl = rtrim(site_base_url(), '/');

$output = "# Learn.Nectra Documentation & Developer Tutorials\n\n";
$output .= "> Learn.Nectra is a reference-grade developer education platform offering modern, interactive, and beginner-to-master tutorials for PHP, Python, JavaScript, HTML5, CSS3, and SQL. Every lesson includes runnable sandbox code examples, syntax breakdown tables, and real-world analogies.\n\n";

$output .= "## Official Documentation Tracks\n\n";

// Query all active courses
$stmtCourses = $pdo->query("SELECT id, title, slug, description, keywords FROM courses ORDER BY id ASC");
$courses = $stmtCourses->fetchAll(PDO::FETCH_ASSOC);

foreach ($courses as $c) {
    $courseUrl = $baseUrl . '/' . course_path($c['slug']);
    $desc = plain_text($c['description']);
    $kw = !empty($c['keywords']) ? " [Keywords: {$c['keywords']}]" : '';
    $output .= "- [{$c['title']}]({$courseUrl}): {$desc}{$kw}\n";
}

$output .= "\nBrowse the course library at {$baseUrl}/index#courses.\n";
echo $output;
