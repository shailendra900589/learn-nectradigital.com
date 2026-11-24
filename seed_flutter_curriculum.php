<?php
// seed_flutter_curriculum.php
// Creates Flutter & Dart Tutorial course and seeds 47 comprehensive, production-grade chapters.

require_once __DIR__ . '/includes/db.php';

echo "=== Seeding Flutter & Dart Curriculum ===\n";

// 1. Ensure or create course
$stmt = $pdo->prepare("SELECT id FROM courses WHERE slug = 'flutter-tutorial' OR slug = 'flutter-dart-tutorial'");
$stmt->execute();
$course = $stmt->fetch();

$courseTitle = 'Flutter & Dart Tutorial';
$courseSlug  = 'flutter-tutorial';
$courseDesc  = 'Master Flutter 3.x and Dart from absolute beginner to production mobile architect. Complete curriculum covering Dart OOP, Impeller Engine, Widgets, Layouts, Material Design 3, Navigation (GoRouter), State Management (Provider, Riverpod, BLoC), REST APIs, SQLite, Animations, Clean Architecture, and an end-to-end full-stack mobile capstone project with live interactive simulator.';
$courseKeywords = 'flutter, dart, flutter tutorial, learn flutter, flutter 3, dart programming, flutter widgets, material 3, gorouter, flutter state management, provider, riverpod, flutter bloc, flutter rest api, sqflite, flutter animations, flutter responsive, android, ios, flutter capstone project';

if ($course) {
    $courseId = (int)$course['id'];
    $stmtUpdate = $pdo->prepare("UPDATE courses SET title = ?, slug = ?, description = ?, keywords = ? WHERE id = ?");
    $stmtUpdate->execute([$courseTitle, $courseSlug, $courseDesc, $courseKeywords, $courseId]);
    echo "Updated existing course (ID: $courseId)\n";
} else {
    $stmtInsert = $pdo->prepare("INSERT INTO courses (title, slug, description, keywords, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmtInsert->execute([$courseTitle, $courseSlug, $courseDesc, $courseKeywords]);
    $courseId = (int)$pdo->lastInsertId();
    echo "Created new course (ID: $courseId)\n";
}

// 2. Clear existing chapters for this course to allow fresh, clean, ordered seeding
$pdo->prepare("DELETE FROM chapters WHERE course_id = ?")->execute([$courseId]);
echo "Cleared old chapters for Course ID: $courseId\n";

// Helper function to insert a chapter with targeted keywords
function insertChapter($pdo, $courseId, $order, $section, $title, $content, $keywords) {
    $stmt = $pdo->prepare("INSERT INTO chapters (course_id, order_index, section_name, chapter_name, content, keywords) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$courseId, $order, $section, $title, $content, $keywords]);
    echo "  [OK] #$order: $title\n";
}

require_once __DIR__ . '/seed_flutter_data.php';

echo "\nAll 47 Flutter chapters seeded successfully!\n";
