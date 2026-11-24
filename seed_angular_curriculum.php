<?php
// seed_angular_curriculum.php
// Creates AngularJS Tutorial course and seeds 47 comprehensive, production-grade chapters.

require_once __DIR__ . '/includes/db.php';

echo "=== Seeding AngularJS Curriculum ===\n";

// 1. Ensure or create course
$stmt = $pdo->prepare("SELECT id FROM courses WHERE slug = 'angular-tutorial' OR slug = 'angularjs-tutorial'");
$stmt->execute();
$course = $stmt->fetch();

$courseTitle = 'AngularJS Tutorial';
$courseSlug  = 'angular-tutorial';
$courseDesc  = 'Master AngularJS (1.8) from absolute zero to enterprise production standard. Comprehensive 47-lesson curriculum covering MVC architecture, two-way data binding, custom directives, isolated scopes, dependency injection, REST APIs with $http, UI-Router, component architecture, testing with Jasmine, and migration to modern Angular.';
$courseKeywords = 'angularjs, angular, angularjs tutorial, learn angularjs, angular directives, angular controllers, dependency injection, angular services, ui-router, ng-model, two way data binding, angular filters, isolated scope, compile vs link, transclusion, karma jasmine, ngupgrade, single page application';

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
    $slug = $order . '-' . strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
    $slug = trim($slug, '-');

    $stmt = $pdo->prepare("INSERT INTO chapters (course_id, order_index, section_name, chapter_name, content, keywords) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$courseId, $order, $section, $title, $content, $keywords]);
    echo "  [OK] #$order: $title\n";
}

require_once __DIR__ . '/seed_angular_data.php';

echo "\nAll AngularJS chapters seeded successfully!\n";
