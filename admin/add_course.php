<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();
require_admin_role($pdo, ['owner', 'editor']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();
    $title = trim($_POST['title'] ?? '');
    $slug = slugify($_POST['slug'] ?? $title, '');
    $description = trim($_POST['description'] ?? '');

    if (!empty($title) && !empty($slug)) {
        try {
            // Prepare statement for secure insertion
            $stmt = $pdo->prepare("INSERT INTO courses (title, slug, description) VALUES (:title, :slug, :description)");
            $stmt->execute([
                'title' => $title,
                'slug' => $slug,
                'description' => $description
            ]);
            $success = "Course created successfully!";
        } catch (PDOException $e) {
            // Check for duplicate slug error (SQLSTATE 23000)
            if ($e->getCode() == 23000) {
                $error = "Error: That SEO Slug is already in use. Please choose another.";
            } else {
                $error = "Database Error. Please check the course details and try again.";
            }
        }
    } else {
        $error = "Title and Slug are required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo admin_styles(); ?>
</head>
<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden text-slate-800">
    <?php echo admin_sidebar($pdo, 'courses'); ?>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="admin-header flex items-center px-8 sticky top-0 z-10">
            <div>
                <a href="courses" class="text-sm text-blue-600 font-bold hover:underline">Back to Courses</a>
                <h2 class="text-2xl font-black text-slate-950">Add New Course</h2>
            </div>
        </header>

        <div class="p-8 max-w-3xl">
            <?php if($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo h($error); ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo h($success); ?></div>
            <?php endif; ?>

            <div class="admin-card p-6">
                <form method="POST" action="">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Course Title</label>
                        <input type="text" id="title" name="title" class="admin-input" placeholder="e.g., PHP for Beginners" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">SEO Slug (URL)</label>
                        <input type="text" id="slug" name="slug" class="admin-input bg-slate-50" placeholder="e.g., php-for-beginners" required>
                        <p class="text-xs text-gray-500 mt-1">This will be the URL: yoursite.com/course/<strong>your-clean-slug</strong></p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Short Description</label>
                        <textarea name="description" rows="4" class="admin-input" placeholder="A brief summary of what this course covers..."></textarea>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="admin-btn-primary">Save Course</button>
                        <a href="courses" class="text-gray-600 hover:text-gray-900 font-bold">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');

        titleInput.addEventListener('input', function() {
            // Convert to lowercase, replace spaces with hyphens, remove special characters
            let slug = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            slugInput.value = slug;
        });
    </script>
</body>
</html>
