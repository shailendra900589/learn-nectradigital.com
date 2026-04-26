<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();

if (!isset($_GET['course_id'])) {
    header("Location: courses");
    exit;
}

$course_id = get_int('course_id');
$error = '';
$success = '';

// Fetch course details
$stmtCourse = $pdo->prepare("SELECT title FROM courses WHERE id = :id");
$stmtCourse->execute(['id' => $course_id]);
$course = $stmtCourse->fetch();
if (!$course) die("Course not found.");

// --- 1. HANDLE DELETE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_chapter') {
    verify_csrf();
    $delete_id = post_int('chapter_id');
    $stmt = $pdo->prepare("DELETE FROM chapters WHERE id = :id AND course_id = :course_id");
    if ($stmt->execute(['id' => $delete_id, 'course_id' => $course_id])) {
        $success = "Chapter deleted successfully!";
    }
}

// --- 2. HANDLE ADD & UPDATE (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') !== 'delete_chapter') {
    verify_csrf();
    $chapter_id = $_POST['chapter_id'] ?? '';
    $chapter_name = trim($_POST['chapter_name'] ?? '');
    $content = $_POST['content'] ?? '';
    $practice_content = substr(trim($_POST['practice_content'] ?? ''), 0, 12000);
    $download_label = substr(trim($_POST['download_label'] ?? ''), 0, 160);
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    $require_quiz_pass = isset($_POST['require_quiz_pass']) ? 1 : 0;
    $quiz = parse_quiz_lines($_POST['quiz_lines'] ?? '');
    $quiz_json = $quiz ? json_encode($quiz) : null;
    $order_index = (int)$_POST['order_index'];
    $download_file = trim($_POST['existing_download_file'] ?? '');

    if (isset($_FILES['download_file']) && $_FILES['download_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['download_file']['error'] !== UPLOAD_ERR_OK) {
            $error = "Download file upload failed.";
        } elseif ($_FILES['download_file']['size'] > 10 * 1024 * 1024) {
            $error = "Download file must be under 10 MB.";
        } else {
            $extension = strtolower(pathinfo($_FILES['download_file']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['pdf', 'zip', 'txt', 'docx', 'xlsx', 'pptx', 'csv'];
            if (!in_array($extension, $allowedExtensions, true)) {
                $error = "Allowed downloads: PDF, ZIP, TXT, DOCX, XLSX, PPTX, CSV.";
            } else {
                $uploadDir = '../assets/uploads/chapter-downloads/';
                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                    $error = "Could not prepare download folder.";
                } else {
                    $fileName = 'chapter-' . ($chapter_id ?: 'new') . '-' . bin2hex(random_bytes(8)) . '.' . $extension;
                    if (move_uploaded_file($_FILES['download_file']['tmp_name'], $uploadDir . $fileName)) {
                        $download_file = 'assets/uploads/chapter-downloads/' . $fileName;
                    } else {
                        $error = "Could not save download file.";
                    }
                }
            }
        }
    }

    if ($error === '' && trim($_POST['quiz_lines'] ?? '') !== '' && count($quiz) < 10) {
        $error = "Add at least 10 valid quiz questions. Students will get 4 random questions per attempt.";
    }

    if ($error === '' && !empty($chapter_name) && !empty($content)) {
        $stmtDuplicate = $pdo->prepare("
            SELECT id
            FROM chapters
            WHERE course_id = :course_id
              AND chapter_name = :chapter_name
              AND order_index = :order_index
              AND id != :chapter_id
            LIMIT 1
        ");
        $stmtDuplicate->execute([
            'course_id' => $course_id,
            'chapter_name' => $chapter_name,
            'order_index' => $order_index,
            'chapter_id' => (int)($chapter_id ?: 0),
        ]);
        if ($stmtDuplicate->fetch()) {
            $error = "A chapter with the same title and position already exists.";
        }
    }

    if ($error === '' && !empty($chapter_name) && !empty($content)) {
        try {
            if (!empty($chapter_id)) {
                // UPDATE Existing Chapter
                $stmt = $pdo->prepare("
                    UPDATE chapters
                    SET chapter_name = :name,
                        content = :content,
                        quiz_json = :quiz_json,
                        practice_content = :practice_content,
                        download_file = :download_file,
                        download_label = :download_label,
                        is_premium = :is_premium,
                        require_quiz_pass = :require_quiz_pass,
                        order_index = :order
                    WHERE id = :id
                ");
                $stmt->execute([
                    'name' => $chapter_name,
                    'content' => $content,
                    'quiz_json' => $quiz_json,
                    'practice_content' => $practice_content,
                    'download_file' => $download_file,
                    'download_label' => $download_label,
                    'is_premium' => $is_premium,
                    'require_quiz_pass' => $require_quiz_pass,
                    'order' => $order_index,
                    'id' => $chapter_id,
                ]);
                $success = "Chapter updated successfully!";
            } else {
                // INSERT New Chapter
                $stmt = $pdo->prepare("
                    INSERT INTO chapters (course_id, chapter_name, content, quiz_json, practice_content, download_file, download_label, is_premium, require_quiz_pass, order_index)
                    VALUES (:course_id, :name, :content, :quiz_json, :practice_content, :download_file, :download_label, :is_premium, :require_quiz_pass, :order)
                ");
                $stmt->execute([
                    'course_id' => $course_id,
                    'name' => $chapter_name,
                    'content' => $content,
                    'quiz_json' => $quiz_json,
                    'practice_content' => $practice_content,
                    'download_file' => $download_file,
                    'download_label' => $download_label,
                    'is_premium' => $is_premium,
                    'require_quiz_pass' => $require_quiz_pass,
                    'order' => $order_index,
                ]);
                $success = "Chapter added successfully!";
            }
        } catch (PDOException $e) {
            $error = "Database Error. Please try again.";
        }
    } elseif ($error === '') {
        $error = "Chapter Name and Content are required.";
    }
}

// --- 3. PREPARE FORM DATA (For Edit Mode) ---
$edit_id = '';
$edit_name = '';
$edit_content = '';
$edit_order = 1;
$edit_quiz_lines = '';
$edit_practice_content = '';
$edit_download_file = '';
$edit_download_label = '';
$edit_is_premium = 0;
$edit_require_quiz_pass = 1;

if (isset($_GET['edit'])) {
    $stmtEdit = $pdo->prepare("SELECT * FROM chapters WHERE id = :id AND course_id = :course_id");
    $stmtEdit->execute(['id' => $_GET['edit'], 'course_id' => $course_id]);
    $chapterToEdit = $stmtEdit->fetch();
    
    if ($chapterToEdit) {
        $edit_id = $chapterToEdit['id'];
        $edit_name = $chapterToEdit['chapter_name'];
        $edit_content = $chapterToEdit['content'];
        $edit_order = $chapterToEdit['order_index'];
        $edit_quiz_lines = quiz_to_lines($chapterToEdit['quiz_json'] ?? '');
        $edit_practice_content = $chapterToEdit['practice_content'] ?? '';
        $edit_download_file = $chapterToEdit['download_file'] ?? '';
        $edit_download_label = $chapterToEdit['download_label'] ?? '';
        $edit_is_premium = (int)($chapterToEdit['is_premium'] ?? 0);
        $edit_require_quiz_pass = (int)($chapterToEdit['require_quiz_pass'] ?? 1);
    }
} else {
    // If not editing, auto-suggest the next order number
    $stmtOrder = $pdo->prepare("SELECT MAX(order_index) as max_order FROM chapters WHERE course_id = :course_id");
    $stmtOrder->execute(['course_id' => $course_id]);
    $maxOrder = $stmtOrder->fetch();
    $edit_order = ($maxOrder['max_order'] ?? 0) + 1;
}

// --- 4. FETCH ALL CHAPTERS FOR SIDEBAR ---
$stmtChapters = $pdo->prepare("
    SELECT MIN(id) AS id, chapter_name, order_index
    FROM chapters
    WHERE course_id = :course_id
    GROUP BY order_index, chapter_name
    ORDER BY order_index ASC, id ASC
");
$stmtChapters->execute(['course_id' => $course_id]);
$chapters = $stmtChapters->fetchAll();
$quizBuilderItems = [];
$decodedQuiz = json_decode((string)($chapterToEdit['quiz_json'] ?? ''), true);
if (is_array($decodedQuiz)) {
    $quizBuilderItems = $decodedQuiz;
}
while (count($quizBuilderItems) < 10) {
    $quizBuilderItems[] = ['question' => '', 'answer' => '', 'options' => ['', '', '']];
}
$quizBuilderItems = array_slice($quizBuilderItems, 0, 25);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Chapters | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <?php echo admin_styles(); ?>
    <style>
        /* Custom Scrollbar for a cleaner look */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .note-editor.note-frame { border: 1px solid #e2e8f0; border-radius: 0.5rem; overflow: hidden; }
        .note-editable { background-color: #1e293b !important; color: #f8fafc !important; }
        .note-toolbar { background-color: #f8fafc !important; border-bottom: 1px solid #e2e8f0; }
        .form-card { border: 1px solid #e2e8f0; border-radius: 0.75rem; background: #fff; }
        .form-input { width: 100%; border: 1px solid #cbd5e1; border-radius: 0.65rem; padding: 0.75rem 0.9rem; outline: none; }
        .form-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14); }
        .quiz-card { border: 1px solid #dbe3ef; border-radius: 0.75rem; background: #f8fafc; padding: 1rem; }
        .quiz-card.is-empty { opacity: 0.72; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden text-slate-800">
    <?php echo admin_sidebar($pdo, 'courses'); ?>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="admin-header flex items-center px-8 sticky top-0 z-10">
            <div>
                <a href="courses" class="text-sm text-blue-600 hover:underline flex items-center gap-1 mb-1">
                    Back to Courses
                </a>
                <h2 class="text-2xl font-black text-slate-950 leading-none">
                    <?php echo htmlspecialchars($course['title']); ?> <span class="text-gray-400 font-normal text-lg">/ Chapters</span>
                </h2>
            </div>
        </header>

        <div class="p-8">
            <?php if($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <?php echo h($error); ?>
                </div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <?php echo h($success); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 admin-card p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800">
                            <?php echo $edit_id ? '<span class="text-blue-600">Edit Chapter</span>' : 'Write New Chapter'; ?>
                        </h3>
                        <?php if($edit_id): ?>
                            <a href="chapters?course_id=<?php echo $course_id; ?>" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 py-1.5 px-3 rounded-md transition font-medium">Clear / Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" action="chapters?course_id=<?php echo (int)$course_id; ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="save_chapter">
                        <input type="hidden" name="chapter_id" value="<?php echo (int)$edit_id; ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                            <div class="md:col-span-3">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Chapter Title</label>
                                <input type="text" name="chapter_name" value="<?php echo htmlspecialchars($edit_name); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow outline-none" placeholder="e.g. PHP Variables" required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Position</label>
                                <input type="number" name="order_index" value="<?php echo $edit_order; ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow outline-none text-center" required>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Tutorial Content</label>
                            <div class="mb-3 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-900">
                                <strong>Auto format tips:</strong> Use <code>## Heading</code>, <code>**bold text**</code>, numbered lines like <code>1. Point</code>, bullet lines like <code>* Point</code>, and <code>[IMAGE: describe image]</code>. The public chapter page will convert it into professional tutorial formatting automatically.
                            </div>
                            <textarea id="content" name="content"><?php echo htmlspecialchars($edit_content); ?></textarea>
                        </div>

                        <div class="mb-8 form-card p-5">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                                <div>
                                    <h4 class="text-xl font-black text-slate-900">Quiz Builder</h4>
                                    <p class="text-sm text-slate-500 mt-1">Fill the 10 question cards below. Add more only when needed, up to 25.</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <label class="inline-flex items-center gap-2 rounded-lg bg-blue-50 border border-blue-100 px-3 py-2 text-sm font-bold text-blue-800">
                                        <input type="checkbox" name="require_quiz_pass" <?php echo $edit_require_quiz_pass ? 'checked' : ''; ?>>
                                        Require pass
                                    </label>
                                    <button type="button" id="addQuizQuestion" class="rounded-lg bg-slate-900 hover:bg-blue-600 text-white px-4 py-2 text-sm font-bold">Add Question</button>
                                </div>
                            </div>
                            <input type="hidden" name="quiz_lines" id="quizLines">
                            <div id="quizBuilder" class="grid grid-cols-1 2xl:grid-cols-2 gap-4">
                                <?php foreach($quizBuilderItems as $index => $item): 
                                    $answer = (string)($item['answer'] ?? '');
                                    $options = array_values(array_filter((array)($item['options'] ?? []), fn($option) => (string)$option !== $answer));
                                    while (count($options) < 3) $options[] = '';
                                ?>
                                    <div class="quiz-card <?php echo trim((string)($item['question'] ?? '')) === '' ? 'is-empty' : ''; ?>" data-quiz-card>
                                        <div class="flex items-center justify-between gap-3 mb-3">
                                            <div class="font-black text-slate-900">Question <span data-question-number><?php echo $index + 1; ?></span></div>
                                            <button type="button" class="text-xs font-bold text-red-600 hover:underline" data-remove-question>Remove</button>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                                            <div class="lg:col-span-2">
                                                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-1">Question</label>
                                                <input type="text" class="form-input" data-quiz-question value="<?php echo h((string)($item['question'] ?? '')); ?>" placeholder="Example: What does PHP stand for?">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black uppercase tracking-wider text-emerald-600 mb-1">Correct Answer</label>
                                                <input type="text" class="form-input border-emerald-200" data-quiz-correct value="<?php echo h($answer); ?>" placeholder="Correct option">
                                            </div>
                                            <?php for($optionIndex = 0; $optionIndex < 3; $optionIndex++): ?>
                                                <div>
                                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-1">Wrong Option <?php echo $optionIndex + 1; ?></label>
                                                    <input type="text" class="form-input" data-quiz-wrong value="<?php echo h((string)$options[$optionIndex]); ?>" placeholder="Wrong option">
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-4 rounded-lg bg-amber-50 border border-amber-100 p-3 text-sm text-amber-800 font-bold">
                                Valid questions need 1 question, 1 correct answer, and 3 wrong options. Minimum 10 valid questions.
                            </div>
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                            <div class="form-card p-5 space-y-4">
                                <h4 class="text-xl font-black text-slate-900">Premium Access</h4>
                                <label class="flex items-center gap-3 text-sm font-bold bg-slate-50 border border-slate-200 rounded-lg p-4">
                                    <input type="checkbox" name="is_premium" class="w-5 h-5" <?php echo $edit_is_premium ? 'checked' : ''; ?>>
                                    Premium chapter access only
                                </label>
                                <p class="text-sm text-slate-500">When enabled, only active paid users can open this chapter and its download.</p>
                            </div>

                            <div class="form-card p-5 space-y-4">
                                <h4 class="text-xl font-black text-slate-900">Download File</h4>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Button Label</label>
                                    <input type="text" name="download_label" value="<?php echo h($edit_download_label); ?>" class="form-input" placeholder="Worksheet PDF / Source files">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">File</label>
                                    <input type="hidden" name="existing_download_file" value="<?php echo h($edit_download_file); ?>">
                                    <input type="file" name="download_file" class="w-full border border-gray-300 rounded-lg p-2 text-sm" accept=".pdf,.zip,.txt,.docx,.xlsx,.pptx,.csv">
                                    <?php if($edit_download_file): ?><p class="text-xs text-blue-600 font-bold mt-2">Current: <?php echo h($edit_download_file); ?></p><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8 rounded-xl border border-gray-200 p-5">
                            <h4 class="font-black text-slate-900 mb-3">Practice Set</h4>
                            <textarea name="practice_content" rows="6" class="form-input text-sm" placeholder="Add practice tasks, exercises, project prompt, or homework for this chapter."><?php echo h($edit_practice_content); ?></textarea>
                        </div>

                        <div class="flex items-center">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all focus:outline-none focus:ring-4 focus:ring-blue-300">
                                <?php echo $edit_id ? 'Update Chapter Content' : 'Publish New Chapter'; ?>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-1">
                    <div class="admin-card overflow-hidden sticky top-24">
                        <div class="bg-slate-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Table of Contents</h3>
                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded-full"><?php echo count($chapters); ?></span>
                        </div>
                        
                        <ul class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                            <?php if (count($chapters) > 0): ?>
                                <?php foreach ($chapters as $chapter): 
                                    $is_editing = ($edit_id == $chapter['id']);
                                ?>
                                    <li class="p-4 hover:bg-slate-50 transition-colors <?php echo $is_editing ? 'bg-blue-50 border-l-4 border-blue-500' : 'border-l-4 border-transparent'; ?>">
                                        <div class="flex justify-between items-start gap-3">
                                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                                <span class="flex-shrink-0 w-6 h-6 rounded-full <?php echo $is_editing ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'; ?> flex items-center justify-center text-xs font-bold mt-0.5">
                                                    <?php echo $chapter['order_index']; ?>
                                                </span>
                                                <p class="text-sm font-medium text-slate-800 truncate" title="<?php echo htmlspecialchars($chapter['chapter_name']); ?>">
                                                    <?php echo htmlspecialchars($chapter['chapter_name']); ?>
                                                </p>
                                            </div>
                                            
                                            <div class="flex items-center gap-1 opacity-80 hover:opacity-100 transition-opacity">
                                                <a href="chapters?course_id=<?php echo $course_id; ?>&edit=<?php echo $chapter['id']; ?>" class="p-1.5 text-blue-600 hover:bg-blue-100 rounded" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </a>
                                                 <form method="POST" action="chapters?course_id=<?php echo (int)$course_id; ?>" onsubmit="return confirm('Are you sure you want to delete this chapter?');">
                                                     <?php echo csrf_field(); ?>
                                                     <input type="hidden" name="action" value="delete_chapter">
                                                     <input type="hidden" name="chapter_id" value="<?php echo (int)$chapter['id']; ?>">
                                                     <button type="submit" class="p-1.5 text-red-500 hover:bg-red-100 rounded" title="Delete">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                     </button>
                                                 </form>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="p-8 text-center text-slate-500 text-sm">
                                    <div class="mb-2">📚</div>
                                    No chapters yet.<br>Write your first one!
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
      $(document).ready(function() {
          $('#content').summernote({
              placeholder: 'Example:\n## What is SEO?\n**SEO** means Search Engine Optimization.\n\n1. Better visibility\n2. Higher trust\n\n[IMAGE: SERP screenshot showing paid ads and organic results]',
              tabsize: 2,
              height: 500,
              toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
              ]
          });

          const quizBuilder = document.getElementById('quizBuilder');
          const addQuizQuestion = document.getElementById('addQuizQuestion');
          const quizLines = document.getElementById('quizLines');
          const chapterForm = document.querySelector('form[action^="chapters"]');

          function renumberQuizCards() {
              quizBuilder.querySelectorAll('[data-quiz-card]').forEach((card, index) => {
                  card.querySelector('[data-question-number]').textContent = String(index + 1);
                  const isEmpty = !card.querySelector('[data-quiz-question]').value.trim();
                  card.classList.toggle('is-empty', isEmpty);
                  card.querySelector('[data-remove-question]').disabled = quizBuilder.querySelectorAll('[data-quiz-card]').length <= 10;
                  card.querySelector('[data-remove-question]').classList.toggle('opacity-40', quizBuilder.querySelectorAll('[data-quiz-card]').length <= 10);
              });
              addQuizQuestion.disabled = quizBuilder.querySelectorAll('[data-quiz-card]').length >= 25;
              addQuizQuestion.classList.toggle('opacity-50', addQuizQuestion.disabled);
          }

          function createQuizCard() {
              const card = document.createElement('div');
              card.className = 'quiz-card is-empty';
              card.setAttribute('data-quiz-card', '');
              card.innerHTML = `
                  <div class="flex items-center justify-between gap-3 mb-3">
                      <div class="font-black text-slate-900">Question <span data-question-number></span></div>
                      <button type="button" class="text-xs font-bold text-red-600 hover:underline" data-remove-question>Remove</button>
                  </div>
                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                      <div class="lg:col-span-2">
                          <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-1">Question</label>
                          <input type="text" class="form-input" data-quiz-question placeholder="Example: What does PHP stand for?">
                      </div>
                      <div>
                          <label class="block text-xs font-black uppercase tracking-wider text-emerald-600 mb-1">Correct Answer</label>
                          <input type="text" class="form-input border-emerald-200" data-quiz-correct placeholder="Correct option">
                      </div>
                      <div>
                          <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-1">Wrong Option 1</label>
                          <input type="text" class="form-input" data-quiz-wrong placeholder="Wrong option">
                      </div>
                      <div>
                          <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-1">Wrong Option 2</label>
                          <input type="text" class="form-input" data-quiz-wrong placeholder="Wrong option">
                      </div>
                      <div>
                          <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-1">Wrong Option 3</label>
                          <input type="text" class="form-input" data-quiz-wrong placeholder="Wrong option">
                      </div>
                  </div>`;
              return card;
          }

          addQuizQuestion?.addEventListener('click', () => {
              if (quizBuilder.querySelectorAll('[data-quiz-card]').length >= 25) return;
              quizBuilder.appendChild(createQuizCard());
              renumberQuizCards();
          });

          quizBuilder?.addEventListener('click', (event) => {
              const removeButton = event.target.closest('[data-remove-question]');
              if (!removeButton || quizBuilder.querySelectorAll('[data-quiz-card]').length <= 10) return;
              removeButton.closest('[data-quiz-card]').remove();
              renumberQuizCards();
          });

          quizBuilder?.addEventListener('input', renumberQuizCards);

          chapterForm?.addEventListener('submit', () => {
              const lines = [];
              quizBuilder.querySelectorAll('[data-quiz-card]').forEach((card) => {
                  const question = card.querySelector('[data-quiz-question]').value.trim();
                  const correct = card.querySelector('[data-quiz-correct]').value.trim();
                  const wrong = Array.from(card.querySelectorAll('[data-quiz-wrong]')).map(input => input.value.trim()).filter(Boolean);
                  if (question && correct && wrong.length >= 3) {
                      lines.push([question, correct, ...wrong.slice(0, 3)].join(' | '));
                  }
              });
              quizLines.value = lines.join('\n');
          });

          renumberQuizCards();
      });
    </script>
</body>
</html>
