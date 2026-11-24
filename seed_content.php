<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';

echo "=== Seeding Full Tutorials Curriculum ===\n";

// 1. Ensure courses exist
$coursesList = [
    [
        'title' => 'PHP Tutorial',
        'slug' => 'php-tutorials',
        'description' => 'Comprehensive reference guide for PHP. Learn server-side programming, syntax, forms, OOP, MySQL databases, and modern APIs with executable examples.'
    ],
    [
        'title' => 'Python Tutorial',
        'slug' => 'python-complete-tutorials',
        'description' => 'Master Python programming from scratch. Covers variables, data structures, OOP, modules, exception handling, and file operations with live code samples.'
    ],
    [
        'title' => 'JavaScript Tutorial',
        'slug' => 'javascript-tutorial',
        'description' => 'Learn modern JavaScript (ES6+) for interactive web applications. Covers variables, functions, DOM manipulation, events, promises, and async/await.'
    ],
    [
        'title' => 'HTML5 Tutorial',
        'slug' => 'html-tutorial',
        'description' => 'The standard markup language for building web pages. Master semantic elements, forms, audio/video, tables, and modern HTML5 layout architecture.'
    ],
    [
        'title' => 'CSS3 Tutorial',
        'slug' => 'css-tutorial',
        'description' => 'Style and design responsive websites. Master selectors, box model, Flexbox, CSS Grid, animations, and modern media queries.'
    ],
    [
        'title' => 'SQL Tutorial',
        'slug' => 'sql-tutorial',
        'description' => 'The standard language for relational databases. Master SELECT queries, WHERE filters, JOINs, GROUP BY, aggregations, and database schemas.'
    ]
];

$courseIds = [];
foreach ($coursesList as $c) {
    $stmtFind = $pdo->prepare("SELECT id FROM courses WHERE slug = :slug");
    $stmtFind->execute(['slug' => $c['slug']]);
    $existingId = $stmtFind->fetchColumn();
    if ($existingId) {
        $stmtUp = $pdo->prepare("UPDATE courses SET title = :title, description = :desc WHERE id = :id");
        $stmtUp->execute(['title' => $c['title'], 'desc' => $c['description'], 'id' => $existingId]);
        $courseIds[$c['slug']] = (int)$existingId;
    } else {
        $stmtIns = $pdo->prepare("INSERT INTO courses (title, slug, description) VALUES (:title, :slug, :desc)");
        $stmtIns->execute(['title' => $c['title'], 'slug' => $c['slug'], 'desc' => $c['description']]);
        $courseIds[$c['slug']] = (int)$pdo->lastInsertId();
    }
}

echo "Courses configured. Course IDs: " . json_encode($courseIds) . "\n";

// Helper function to insert or update a chapter
function upsertChapter(PDO $pdo, int $courseId, int $order, string $section, string $name, string $content, array $quizzes): void {
    $quizJson = json_encode($quizzes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    // Check if chapter exists by course_id and order_index
    $stmt = $pdo->prepare("SELECT id FROM chapters WHERE course_id = :cid AND order_index = :ord LIMIT 1");
    $stmt->execute(['cid' => $courseId, 'ord' => $order]);
    $existingId = $stmt->fetchColumn();

    if ($existingId) {
        $stmtUp = $pdo->prepare("
            UPDATE chapters 
            SET chapter_name = :name, section_name = :section, content = :content, quiz_json = :quiz, require_quiz_pass = 1
            WHERE id = :id
        ");
        $stmtUp->execute([
            'name' => $name,
            'section' => $section,
            'content' => $content,
            'quiz' => $quizJson,
            'id' => $existingId
        ]);
    } else {
        $stmtIns = $pdo->prepare("
            INSERT INTO chapters (course_id, chapter_name, section_name, content, quiz_json, order_index, require_quiz_pass)
            VALUES (:cid, :name, :section, :content, :quiz, :ord, 1)
        ");
        $stmtIns->execute([
            'cid' => $courseId,
            'name' => $name,
            'section' => $section,
            'content' => $content,
            'quiz' => $quizJson,
            'ord' => $order
        ]);
    }
}

// Clean up duplicate old placeholder chapters for PHP and Python if order_index > 24
$pdo->prepare("DELETE FROM chapters WHERE course_id = :cid AND order_index > 24")->execute(['cid' => $courseIds['php-tutorials']]);
$pdo->prepare("DELETE FROM chapters WHERE course_id = :cid AND order_index > 24")->execute(['cid' => $courseIds['python-complete-tutorials']]);

echo "Database cleaned and prepared for chapters.\n";
