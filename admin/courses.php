<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();

$error = '';
$success = '';
$edit_course = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $course_id = post_int('course_id');

    if ($action === 'delete_course') {
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
    } elseif ($action === 'update_course') {
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

// Fetch all courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
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
            <a href="add_course" class="admin-btn-primary">Add New Course</a>
        </header>

        <div class="p-8">
            <?php if($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6"><?php echo h($error); ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6"><?php echo h($success); ?></div>
            <?php endif; ?>

            <?php if($edit_course): ?>
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

            <div class="admin-card overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SEO Slug</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (count($courses) > 0): ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $course['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($course['slug']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="chapters.php?course_id=<?php echo $course['id']; ?>" class="text-green-600 hover:text-green-900 mr-3">Manage Chapters</a>
                                        <a href="courses?edit=<?php echo (int)$course['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        <form method="POST" action="courses" class="inline" onsubmit="return confirm('Delete this course and its chapters?')">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="action" value="delete_course">
                                            <input type="hidden" name="course_id" value="<?php echo (int)$course['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No courses found. Click "Add New Course" to get started.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
