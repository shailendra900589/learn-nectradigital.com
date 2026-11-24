<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/includes/security.php';

$sources = [
    'Setup' => 'https://pytorch.org/get-started/locally/',
    'NumPy' => 'https://numpy.org/doc/stable/user/index.html',
    'pandas' => 'https://pandas.pydata.org/docs/user_guide/index.html',
    'scikit-learn' => 'https://scikit-learn.org/stable/user_guide.html',
    'PyTorch' => 'https://docs.pytorch.org/tutorials/',
    'Capstone' => 'https://scikit-learn.org/stable/common_pitfalls.html',
];

$lessons = [];
foreach (['foundations', 'models'] as $file) {
    $text = require __DIR__ . "/includes/python_library_{$file}.php";
    $blocks = preg_split('/^@@ /m', str_replace("\r\n", "\n", $text), -1, PREG_SPLIT_NO_EMPTY);
    foreach ($blocks as $block) {
        [$metadata, $code] = explode("\n---\n", trim($block), 2);
        [$heading, $explanation, $exercise, $quizText] = explode("\n", $metadata);
        [$section, $title] = explode(' | ', $heading, 2);
        [$question, $correct, $incorrect1, $incorrect2] = explode(' | ', $quizText);
        if (isset($lessons[$title]) || trim($code) === '' || !isset($sources[$section])) {
            throw new RuntimeException("Invalid or duplicate Python lesson: {$title}");
        }

        $shell = $section === 'Setup';
        $language = $shell ? 'powershell' : 'python';
        $runner = match ($section) {
            'Setup' => 'terminal-local',
            'PyTorch', 'Capstone' => 'python-local',
            default => 'python-browser',
        };
        $html = '<p class="lead">' . h($explanation) . '</p>'
            . '<h3>' . ($shell ? 'Set up your environment' : 'Run the example') . '</h3>';
        if (!$shell) {
            $html .= '<p>Save the code as <code>lesson.py</code>. In the course environment, run <code>python lesson.py</code>. '
                . 'On Windows, you can replace <code>python</code> with <code>.\.venv\Scripts\python.exe</code> when the environment is not activated.</p>';
        }
        $html .= '<pre data-course-runner="' . $runner . '"><code class="language-' . $language . '">' . h($code) . '</code></pre>'
            . '<p><a href="' . h($sources[$section]) . '">Official documentation</a></p>';
        $lessons[$title] = [
            'section' => 'Python Libraries · ' . $section,
            'html' => $html,
            'practice' => '<p>' . h($exercise) . '</p>',
            'quiz' => json_encode([[
                'question' => $question,
                'answer' => $correct,
                'options' => [$incorrect1, $correct, $incorrect2],
            ]], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        ];
    }
}

require __DIR__ . '/includes/db.php';
if (!isset($pdo)) {
    throw new RuntimeException('The course database is unavailable.');
}

$pdo->beginTransaction();
try {
    $courseQuery = $pdo->prepare('SELECT id, description, keywords FROM courses WHERE slug = ? FOR UPDATE');
    $courseQuery->execute(['python-complete-tutorials']);
    $course = $courseQuery->fetch();
    if (!$course) {
        throw new RuntimeException('The existing Python course was not found.');
    }

    $find = $pdo->prepare('SELECT id, chapter_name, order_index FROM chapters WHERE course_id = ? ORDER BY order_index, id');
    $find->execute([$course['id']]);
    $chapters = $find->fetchAll();
    $existing = array_column($chapters, 'id', 'chapter_name');
    $order = $chapters ? (int)end($chapters)['order_index'] : 0;
    $insert = $pdo->prepare('INSERT INTO chapters (course_id, order_index, section_name, chapter_name, content, keywords, quiz_json, practice_content, is_premium, require_quiz_pass) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0)');
    $update = $pdo->prepare('UPDATE chapters SET section_name = ?, content = ?, keywords = ?, quiz_json = ?, practice_content = ? WHERE id = ? AND course_id = ?');

    $added = 0;
    foreach ($lessons as $title => $lesson) {
        $values = [$lesson['section'], $lesson['html'], "{$lesson['section']}, {$title}, Python", $lesson['quiz'], $lesson['practice']];
        if (isset($existing[$title])) {
            $update->execute([...$values, $existing[$title], $course['id']]);
        } else {
            $insert->execute([$course['id'], ++$order, $lesson['section'], $title, ...array_slice($values, 1)]);
            ++$added;
        }
    }

    $summary = 'Build practical skills with NumPy, pandas, scikit-learn, and PyTorch through beginner-to-advanced labs and an integrated capstone.';
    $description = str_replace(
        ['Advanced track kept in place after the original lessons:', ' No earlier chapter was removed.'],
        ['Explore advanced API development, including', ''],
        (string)$course['description']
    );
    $description = trim($description);
    if (!str_contains($description, $summary)) {
        $description = trim($description . ' ' . $summary);
    }
    $keywords = array_filter(array_map('trim', explode(',', (string)$course['keywords'])));
    $keywords = implode(', ', array_unique([...$keywords, 'NumPy', 'pandas', 'scikit-learn', 'PyTorch']));
    $updateCourse = $pdo->prepare('UPDATE courses SET description = ?, keywords = ? WHERE id = ?');
    $updateCourse->execute([$description, $keywords, $course['id']]);

    $pdo->commit();
    printf("Python course %d: %d added, %d refreshed.\n", $course['id'], $added, count($lessons) - $added);
} catch (Throwable $error) {
    $pdo->rollBack();
    throw $error;
}
