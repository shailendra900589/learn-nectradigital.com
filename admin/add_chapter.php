<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();
require_admin_role($pdo, ['owner', 'editor']);

if (!isset($_GET['course_id'])) {
    header("Location: courses");
    exit;
}

$course_id = get_int('course_id');
$stmtCourse = $pdo->prepare("SELECT id FROM courses WHERE id = :id");
$stmtCourse->execute(['id' => $course_id]);
if (!$stmtCourse->fetch()) {
    redirect('courses');
}
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();
    $chapter_name = trim($_POST['chapter_name'] ?? '');
    $content = $_POST['content'] ?? ''; 
    $order_index = (int)$_POST['order_index'];

    if (!empty($chapter_name) && !empty($content)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO chapters (course_id, chapter_name, content, order_index) VALUES (:course_id, :chapter_name, :content, :order_index)");
            $stmt->execute([
                'course_id' => $course_id,
                'chapter_name' => $chapter_name,
                'content' => $content,
                'order_index' => $order_index
            ]);
            $success = "Chapter added successfully!";
        } catch (PDOException $e) {
            $error = "Database Error. Please try again.";
        }
    } else {
        $error = "Chapter Name and Content are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Chapter | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <?php echo admin_styles(); ?>

    <style>
        .note-editor.note-frame {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
        }
        .note-editable {
            background-color: #222222 !important; /* Dark background */
            color: #ffffff !important; /* White text */
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden text-slate-800">
    <?php echo admin_sidebar($pdo, 'courses'); ?>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="admin-header flex items-center px-8 sticky top-0 z-10">
            <div>
                <a href="chapters?course_id=<?php echo (int)$course_id; ?>" class="text-sm text-blue-600 font-bold hover:underline">Back to Chapters</a>
                <h2 class="text-2xl font-black text-slate-950">Add New Chapter</h2>
            </div>
        </header>

        <div class="p-8">
            <?php if($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo h($error); ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo h($success); ?></div>
            <?php endif; ?>

            <div class="admin-card p-6">
                <form method="POST" action="">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Chapter Name</label>
                            <input type="text" name="chapter_name" class="admin-input" placeholder="e.g., Introduction to Variables" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Order/Position (Number)</label>
                            <input type="number" name="order_index" value="1" class="admin-input" required>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tutorial Content</label>
                        <textarea id="content" name="content"></textarea>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="admin-btn-primary">Save Chapter</button>
                        <a href="chapters?course_id=<?php echo (int)$course_id; ?>" class="text-gray-600 hover:text-gray-900 font-bold">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
      $(document).ready(function() {
          $('#content').summernote({
              placeholder: 'Write your tutorial content here...',
              tabsize: 2,
              height: 500,
              toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
              ]
          });
      });
    </script>
</body>
</html>
