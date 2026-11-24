<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/db.php';

set_time_limit(0);
$GLOBALS['nectra_translation_pace_us'] = 120000;

$requested = strtolower(trim((string)($argv[1] ?? '')));
$langs = $requested !== '' && isset(supported_languages()[$requested]) && $requested !== 'en'
    ? [$requested]
    : prefixed_language_codes();
$onlyId = isset($argv[2]) ? (int)$argv[2] : 0;

$courses = $pdo->query('SELECT title, description FROM courses')->fetchAll();
$chapters = $pdo->query('SELECT id, chapter_name, section_name, content, practice_content, quiz_json FROM chapters ORDER BY id')->fetchAll();

foreach ($langs as $lang) {
    translation_memory_reset();
    $bag = [];
    foreach ($courses as $course) {
        $bag[] = (string)$course['title'];
        $bag[] = (string)$course['description'];
    }
    localize_texts($bag, $lang);
    echo $lang . " courses\n";

    foreach ($chapters as $chapter) {
        $id = (int)$chapter['id'];
        if ($onlyId > 0 && $id !== $onlyId) {
            continue;
        }
        translation_memory_reset();
        $strings = [
            (string)$chapter['chapter_name'],
            (string)($chapter['section_name'] ?: 'Tutorial'),
        ];
        $quiz = json_decode((string)($chapter['quiz_json'] ?? ''), true);
        if (is_array($quiz)) {
            foreach ($quiz as $item) {
                if (!is_array($item)) {
                    continue;
                }
                if (!empty($item['question'])) {
                    $strings[] = (string)$item['question'];
                }
                foreach ((array)($item['options'] ?? []) as $option) {
                    $strings[] = (string)$option;
                }
            }
        }
        localize_texts($strings, $lang);
        localize_lesson_html(render_chapter_content((string)$chapter['content']), $lang);
        if (!empty($chapter['practice_content'])) {
            localize_lesson_html(render_chapter_content((string)$chapter['practice_content']), $lang);
        }
        echo $lang . ' ' . $id . "\n";
    }
}

echo "done\n";
