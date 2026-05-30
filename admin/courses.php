<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();
require_admin_role($pdo, ['owner', 'editor', 'reviewer']);

$error = '';
$success = '';
$edit_course = null;
$adminRole = current_admin_role($pdo);
$canModifyCourses = in_array($adminRole, ['owner', 'editor'], true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$canModifyCourses) {
        $error = 'Your reviewer role can view courses but cannot modify them.';
    }
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $course_id = post_int('course_id');

    if ($error === '' && $action === 'delete_course') {
        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM student_progress WHERE course_id = :id")->execute(['id' => $course_id]);
            $pdo->prepare("DELETE FROM chapters WHERE course_id = :id")->execute(['id' => $course_id]);
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = :id");
            $stmt->execute(['id' => $course_id]);
            $pdo->commit();
            $success = "Course deleted successfully.";
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Course could not be deleted.";
        }
    } elseif ($error === '' && $action === 'update_course') {
        $title = trim($_POST['title'] ?? '');
        $slug = slugify($_POST['slug'] ?? $title, '');
        $description = trim($_POST['description'] ?? '');

        if ($course_id > 0 && $title !== '' && $slug !== '') {
            try {
                $stmt = $pdo->prepare("UPDATE courses SET title = :title, slug = :slug, description = :description WHERE id = :id");
                $stmt->execute([
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $description,
                    'id' => $course_id,
                ]);
                $success = "Course updated successfully.";
            } catch (PDOException $e) {
                $error = $e->getCode() == 23000 ? "That slug is already used by another course." : "Course could not be updated.";
            }
        } else {
            $error = "Title and slug are required.";
        }
    }
}

if (isset($_GET['edit'])) {
    $stmtEdit = $pdo->prepare("SELECT * FROM courses WHERE id = :id");
    $stmtEdit->execute(['id' => get_int('edit')]);
    $edit_course = $stmtEdit->fetch();
}

// Fetch all courses with editorial stats
$stmt = $pdo->query("
    SELECT c.*,
           COUNT(ch.id) AS chapter_count,
           SUM(CASE WHEN ch.quiz_json IS NOT NULL AND ch.quiz_json != '' THEN 1 ELSE 0 END) AS quiz_ready_count,
           MAX(ch.id) AS latest_chapter_id
    FROM courses c
    LEFT JOIN chapters ch ON ch.course_id = c.id
    GROUP BY c.id
    ORDER BY c.created_at DESC
");
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo admin_styles(); ?>
</head>
<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden text-slate-800">
    <?php echo admin_sidebar($pdo, 'courses'); ?>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="admin-header flex items-center justify-between px-8 sticky top-0 z-10">
            <div>
                <div class="text-sm font-black text-blue-600 uppercase tracking-widest">Courses</div>
                <h2 class="text-2xl font-black text-slate-950">Manage Courses</h2>
            </div>
            <?php if($canModifyCourses): ?>
                <a href="add_course" class="admin-btn-primary">Add New Course</a>
            <?php else: ?>
                <span class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 font-black text-sm">Reviewer mode</span>
            <?php endif; ?>
        </header>

        <div class="p-8">
            <?php if($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6"><?php echo h($error); ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6"><?php echo h($success); ?></div>
            <?php endif; ?>

            <?php if($edit_course && $canModifyCourses): ?>
                <div class="admin-card p-6 mb-6 max-w-3xl">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Course</h3>
                    <form method="POST" action="courses" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="update_course">
                        <input type="hidden" name="course_id" value="<?php echo (int)$edit_course['id']; ?>">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                            <input type="text" name="title" value="<?php echo h($edit_course['title']); ?>" class="admin-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="<?php echo h($edit_course['slug']); ?>" class="admin-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" class="admin-input"><?php echo h($edit_course['description']); ?></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="admin-btn-primary">Save Changes</button>
                            <a href="courses" class="px-5 py-3 rounded-xl border border-gray-300 text-gray-700 font-bold">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="admin-card p-5 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Editor Workflow</h3>
                        <p class="text-sm text-slate-500 mt-1">Use quick actions to jump straight into chapter writing and content QA.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <?php if($canModifyCourses): ?><a href="add_course" class="admin-btn-primary">Create Course</a><?php endif; ?>
                        <?php if (!empty($courses)): ?>
                            <a href="chapters?course_id=<?php echo (int)$courses[0]['id']; ?>" class="px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-black hover:bg-slate-50">Edit Latest Course</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="admin-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                    <h3 class="font-black text-slate-900">Course Editorial Matrix</h3>
                    <input id="courseFilterInput" type="text" placeholder="Filter courses by title or slug..." class="admin-input max-w-sm py-2.5">
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SEO Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Editorial Coverage</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="coursesTableBody">
                        <?php if (count($courses) > 0): ?>
                            <?php foreach ($courses as $course): ?>
                                <?php
                                $chapterCount = (int)$course['chapter_count'];
                                $quizReady = (int)$course['quiz_ready_count'];
                                $coverage = $chapterCount > 0 ? round(($quizReady / $chapterCount) * 100) : 0;
                                ?>
                                <tr data-course-row data-course-keywords="<?php echo h(strtolower($course['title'] . ' ' . $course['slug'])); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $course['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($course['slug']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-black text-slate-700"><?php echo $chapterCount; ?> chapters</span>
                                            <span class="text-xs px-2 py-0.5 rounded-full border <?php echo $coverage >= 70 ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100'; ?>">
                                                <?php echo $coverage; ?>% quiz-ready
                                            </span>
                                        </div>
                                        <div class="w-40 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-1.5 <?php echo $coverage >= 70 ? 'bg-emerald-500' : 'bg-amber-500'; ?>" style="width: <?php echo $coverage; ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="chapters.php?course_id=<?php echo $course['id']; ?>" class="text-green-600 hover:text-green-900 mr-3">Manage Chapters</a>
                                        <?php if($canModifyCourses): ?>
                                            <a href="courses?edit=<?php echo (int)$course['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                            <form method="POST" action="courses" class="inline" onsubmit="return confirm('Delete this course and its chapters?')">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="delete_course">
                                                <input type="hidden" name="course_id" value="<?php echo (int)$course['id']; ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-xs font-black text-slate-400">Review only</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No courses found. Click "Add New Course" to get started.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>
        const courseFilterInput = document.getElementById('courseFilterInput');
        const courseRows = Array.from(document.querySelectorAll('[data-course-row]'));
        courseFilterInput?.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            courseRows.forEach((row) => {
                const key = row.getAttribute('data-course-keywords') || '';
                row.style.display = key.includes(query) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
