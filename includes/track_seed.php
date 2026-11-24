<?php
declare(strict_types=1);

require_once __DIR__ . '/beginner_lesson.php';
require_once __DIR__ . '/seed_helper.php';

/**
 * One beginner-to-master lesson: lead, one careful section, a small-piece table,
 * two runnable examples, micro points, a quiz, and a practice.
 */
function track_lesson(string $lang, string $file, array $c): array
{
    return blesson([
        'section' => $c['section'],
        'title' => $c['title'],
        'keywords' => $c['keywords'],
        'lead' => $c['lead'],
        'sections' => [[
            'heading' => $c['heading'],
            'paragraphs' => [$c['para']],
            'bullets' => $c['bullets'],
        ]],
        'visual' => $c['visual'] ?? '',
        'rows' => $c['rows'],
        'examples' => [
            [$c['e1'][0], $file, $c['e1'][1], $c['e1'][2]],
            [$c['e2'][0], $file, $c['e2'][1], $c['e2'][2]],
        ],
        'micro' => $c['micro'],
        'quiz' => $c['quiz'],
        'practice' => $c['practice'],
    ], $lang);
}

/**
 * Creates a course when it is missing. Inserts only chapter titles that are not
 * already stored. Never deletes a chapter.
 */
function track_seed(PDO $pdo, array $meta, array $chapters): int
{
    $find = $pdo->prepare('SELECT id FROM courses WHERE slug = ? LIMIT 1');
    $find->execute([$meta['slug']]);
    $existing = $find->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        $ins = $pdo->prepare('INSERT INTO courses (title, slug, description, keywords, created_at) VALUES (?, ?, ?, ?, NOW())');
        $ins->execute([$meta['title'], $meta['slug'], $meta['description'], $meta['keywords']]);
        $courseId = (int)$pdo->lastInsertId();
        $names = [];
        $max = 0;
    } else {
        $courseId = (int)$existing['id'];
        $upd = $pdo->prepare('UPDATE courses SET title = ?, description = ?, keywords = ? WHERE id = ?');
        $upd->execute([$meta['title'], $meta['description'], $meta['keywords'], $courseId]);
        $have = $pdo->prepare('SELECT chapter_name FROM chapters WHERE course_id = ?');
        $have->execute([$courseId]);
        $names = array_flip(array_column($have->fetchAll(PDO::FETCH_ASSOC), 'chapter_name'));
        $max = (int)$pdo->query('SELECT COALESCE(MAX(order_index), 0) FROM chapters WHERE course_id = ' . $courseId)->fetchColumn();
    }

    $add = $pdo->prepare('INSERT INTO chapters (course_id, order_index, section_name, chapter_name, content, keywords, quiz_json, practice_content, is_premium, require_quiz_pass) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0)');
    $added = 0;
    foreach ($chapters as $ch) {
        if (isset($names[$ch['title']])) {
            continue;
        }
        $max++;
        $html = build_rich_chapter_html($ch);
        $quiz = !empty($ch['quiz']) ? json_encode($ch['quiz'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
        $add->execute([$courseId, $max, $ch['section'], $ch['title'], $html, $ch['keywords'], $quiz, $ch['practice'] ?? null]);
        $added++;
    }
    echo "[TRACK] {$meta['slug']} +{$added} (course {$courseId}, total new titles this run)\n";
    return $added;
}

function track_place(PDO $pdo, string $slug, array $titles): void
{
    $find = $pdo->prepare('SELECT id FROM courses WHERE slug = ? LIMIT 1');
    $find->execute([$slug]);
    $courseId = (int)$find->fetchColumn();
    if ($courseId === 0) {
        echo "[PLACE] missing course {$slug}\n";
        return;
    }
    $rows = $pdo->prepare('SELECT id, chapter_name FROM chapters WHERE course_id = ?');
    $rows->execute([$courseId]);
    $byName = [];
    foreach ($rows as $row) {
        $byName[$row['chapter_name']][] = (int)$row['id'];
    }
    $order = 0;
    $used = [];
    $upd = $pdo->prepare('UPDATE chapters SET order_index = ? WHERE id = ?');
    foreach ($titles as $title) {
        if (!isset($byName[$title])) {
            echo "[PLACE] {$slug} missing title: {$title}\n";
            continue;
        }
        foreach ($byName[$title] as $id) {
            if (isset($used[$id])) {
                continue;
            }
            $order++;
            $upd->execute([$order, $id]);
            $used[$id] = true;
        }
    }
    foreach ($byName as $name => $ids) {
        foreach ($ids as $id) {
            if (!isset($used[$id])) {
                $order++;
                $upd->execute([$order, $id]);
                echo "[PLACE] {$slug} kept unordered at end: {$name}\n";
            }
        }
    }
    echo "[PLACE] {$slug} ordered {$order}\n";
}

/**
 * Rewrites the HTML of chapters that already exist. Matches on the title.
 * Does not delete a chapter and does not change its id.
 */
function track_refresh(PDO $pdo, string $slug, array $chapters): int
{
    $find = $pdo->prepare('SELECT id FROM courses WHERE slug = ? LIMIT 1');
    $find->execute([$slug]);
    $courseId = (int)$find->fetchColumn();
    if ($courseId === 0) {
        echo "[REFRESH] {$slug} missing course\n";
        return 0;
    }
    $upd = $pdo->prepare('UPDATE chapters SET content = ? WHERE course_id = ? AND chapter_name = ?');
    $done = 0;
    foreach ($chapters as $ch) {
        $html = build_rich_chapter_html($ch);
        $upd->execute([$html, $courseId, $ch['title']]);
        if ($upd->rowCount() < 1) {
            $check = $pdo->prepare('SELECT id FROM chapters WHERE course_id = ? AND chapter_name = ? LIMIT 1');
            $check->execute([$courseId, $ch['title']]);
            if (!$check->fetchColumn()) {
                echo "[REFRESH] {$slug} missing title: {$ch['title']}\n";
                continue;
            }
        }
        $done++;
    }
    echo "[REFRESH] {$slug} refreshed {$done}\n";
    return $done;
}

function track_sections(PDO $pdo, string $slug, array $ranges): void
{
    $find = $pdo->prepare('SELECT id FROM courses WHERE slug = ? LIMIT 1');
    $find->execute([$slug]);
    $courseId = (int)$find->fetchColumn();
    if ($courseId === 0) {
        return;
    }
    $upd = $pdo->prepare('UPDATE chapters SET section_name = ? WHERE course_id = ? AND order_index = ?');
    $end = 0;
    foreach ($ranges as $last => $name) {
        for ($i = $end + 1; $i <= $last; $i++) {
            $upd->execute([$name, $courseId, $i]);
        }
        $end = $last;
    }
}
