<?php
// seed_nextjs_curriculum.php
// Creates Next.js Tutorial course and seeds 47 comprehensive, production-grade chapters.

require_once __DIR__ . '/includes/db.php';

echo "=== Seeding Next.js Curriculum ===\n";

// 1. Ensure or create course
$stmt = $pdo->prepare("SELECT id FROM courses WHERE slug = 'nextjs-tutorial' OR slug = 'next-tutorial'");
$stmt->execute();
$course = $stmt->fetch();

$courseTitle = 'Next.js Tutorial';
$courseSlug  = 'nextjs-tutorial';
$courseDesc  = 'Master Next.js 14 and 15 from absolute beginner to enterprise full-stack architect. Complete curriculum covering App Router, React Server Components (RSC), Server Actions, Streaming SSR with Suspense, Route Handlers, Caching & Revalidation, Middleware, Auth & Security, and an end-to-end full-stack project with live interactive demo.';
$courseKeywords = 'nextjs, next.js, nextjs tutorial, learn nextjs, app router, react server components, rsc, server actions, nextjs 14, nextjs 15, route handlers, dynamic routes, streaming ssr, suspense, nextjs caching, middleware, nextauth, full stack nextjs, vercel, nextjs project';

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

require_once __DIR__ . '/seed_nextjs_data.php';

echo "\nAll Next.js chapters seeded successfully!\n";
