<?php
// seed_nextjs_data.php
require_once __DIR__ . '/seed_nextjs_part1.php';
require_once __DIR__ . '/seed_nextjs_part2.php';
require_once __DIR__ . '/seed_nextjs_part3.php';
require_once __DIR__ . '/seed_nextjs_part4.php';

$part4 = getNextJsPart4();
foreach ($part4 as $ch) {
    insertChapter($pdo, $courseId, $ch['order'], $ch['section'], $ch['name'], $ch['content'], $ch['keywords']);
}
