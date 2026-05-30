<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();
require_admin_role($pdo, ['owner', 'editor', 'reviewer']);

if (!isset($_GET['course_id'])) {
    header("Location: courses");
    exit;
}

$course_id = get_int('course_id');
$error = '';
$success = '';
$adminRole = current_admin_role($pdo);
$canEditContent = admin_can_edit_content($pdo);
$canReviewContent = admin_can_review_content($pdo);

// Fetch course details
$stmtCourse = $pdo->prepare("SELECT title FROM courses WHERE id = :id");
$stmtCourse->execute(['id' => $course_id]);
$course = $stmtCourse->fetch();
if (!$course) die("Course not found.");

// --- 1. HANDLE DELETE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_chapter') {
    if (!$canEditContent) {
        $error = 'Reviewer role cannot delete chapters.';
    }
    verify_csrf();
    $delete_id = post_int('chapter_id');
    if ($error === '') {
        $stmt = $pdo->prepare("DELETE FROM chapters WHERE id = :id AND course_id = :course_id");
        if ($stmt->execute(['id' => $delete_id, 'course_id' => $course_id])) {
            $success = "Chapter deleted successfully!";
        }
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
    $editorial_status = $_POST['editorial_status'] ?? 'draft';
    $review_notes = substr(trim($_POST['review_notes'] ?? ''), 0, 2000);
    if (!in_array($editorial_status, ['draft', 'in_review', 'published'], true)) {
        $editorial_status = 'draft';
    }

    if ($adminRole === 'editor' && $editorial_status === 'published') {
        $editorial_status = 'in_review';
    }
    if ($adminRole === 'reviewer' && !$chapter_id) {
        $error = 'Reviewer role can only review existing chapters.';
    }

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

    if ($error === '' && $canEditContent && trim($_POST['quiz_lines'] ?? '') !== '' && count($quiz) < 10) {
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
                $stmtExisting = $pdo->prepare("SELECT * FROM chapters WHERE id = :id AND course_id = :course_id LIMIT 1");
                $stmtExisting->execute(['id' => $chapter_id, 'course_id' => $course_id]);
                $existingChapter = $stmtExisting->fetch();
                if (!$existingChapter) {
                    $error = "Chapter not found.";
                }
            }

            if ($error === '' && !empty($chapter_id)) {
                if (!$canEditContent && $canReviewContent) {
                    $stmt = $pdo->prepare("
                        UPDATE chapters
                        SET editorial_status = :editorial_status,
                            review_notes = :review_notes,
                            reviewed_by_admin_id = :reviewed_by,
                            reviewed_at = NOW(),
                            published_at = CASE WHEN :editorial_status = 'published' THEN NOW() ELSE published_at END
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        'editorial_status' => $editorial_status,
                        'review_notes' => $review_notes,
                        'reviewed_by' => (int)($_SESSION['admin_id'] ?? 0),
                        'id' => $chapter_id,
                    ]);
                    $success = "Chapter review workflow updated.";
                } else {
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
                        order_index = :order,
                        editorial_status = :editorial_status,
                        review_notes = :review_notes,
                        updated_by_admin_id = :updated_by,
                        reviewed_by_admin_id = CASE WHEN :reviewed_now = 1 THEN :updated_by ELSE reviewed_by_admin_id END,
                        reviewed_at = CASE WHEN :reviewed_now = 1 THEN NOW() ELSE reviewed_at END,
                        published_at = CASE WHEN :editorial_status = 'published' THEN NOW() ELSE published_at END
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
                    'editorial_status' => $editorial_status,
                    'review_notes' => $review_notes,
                    'updated_by' => (int)($_SESSION['admin_id'] ?? 0),
                    'reviewed_now' => $canReviewContent ? 1 : 0,
                    'id' => $chapter_id,
                ]);
                $success = "Chapter updated successfully!";
                }
            } else {
                if (!$canEditContent) {
                    $error = "Reviewer role cannot create chapters.";
                }
            }

            if ($error === '' && empty($chapter_id)) {
                // INSERT New Chapter
                $stmt = $pdo->prepare("
                    INSERT INTO chapters (course_id, chapter_name, content, quiz_json, practice_content, download_file, download_label, is_premium, require_quiz_pass, order_index, editorial_status, review_notes, updated_by_admin_id)
                    VALUES (:course_id, :name, :content, :quiz_json, :practice_content, :download_file, :download_label, :is_premium, :require_quiz_pass, :order, :editorial_status, :review_notes, :updated_by)
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
                    'editorial_status' => $editorial_status,
                    'review_notes' => $review_notes,
                    'updated_by' => (int)($_SESSION['admin_id'] ?? 0),
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
$edit_editorial_status = 'draft';
$edit_review_notes = '';
$chapterToEdit = ['quiz_json' => null];

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
        $edit_editorial_status = (string)($chapterToEdit['editorial_status'] ?? 'draft');
        $edit_review_notes = (string)($chapterToEdit['review_notes'] ?? '');
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
    SELECT id, chapter_name, order_index,
           (quiz_json IS NOT NULL AND quiz_json != '') AS has_quiz,
           (practice_content IS NOT NULL AND TRIM(practice_content) != '') AS has_practice,
           (download_file IS NOT NULL AND TRIM(download_file) != '') AS has_download,
           editorial_status,
           is_premium,
           LENGTH(content) AS content_size
    FROM chapters
    WHERE course_id = :course_id
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
        .editor-metric { border: 1px solid #e2e8f0; border-radius: .75rem; padding: .75rem; background: #fff; }
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

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="editor-metric">
                    <div class="text-xs font-black uppercase tracking-widest text-slate-400">Total Chapters</div>
                    <div class="text-2xl font-black text-slate-900 mt-1"><?php echo number_format(count($chapters)); ?></div>
                </div>
                <div class="editor-metric">
                    <div class="text-xs font-black uppercase tracking-widest text-slate-400">Quiz Coverage</div>
                    <div class="text-2xl font-black text-blue-700 mt-1"><?php echo number_format(count(array_filter($chapters, fn($c) => (int)$c['has_quiz'] === 1))); ?></div>
                </div>
                <div class="editor-metric">
                    <div class="text-xs font-black uppercase tracking-widest text-slate-400">Practice Coverage</div>
                    <div class="text-2xl font-black text-emerald-700 mt-1"><?php echo number_format(count(array_filter($chapters, fn($c) => (int)$c['has_practice'] === 1))); ?></div>
                </div>
                <div class="editor-metric">
                    <div class="text-xs font-black uppercase tracking-widest text-slate-400">Download Coverage</div>
                    <div class="text-2xl font-black text-violet-700 mt-1"><?php echo number_format(count(array_filter($chapters, fn($c) => (int)$c['has_download'] === 1))); ?></div>
                </div>
            </div>

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
                        
                        <?php if(!$canEditContent && $canReviewContent): ?>
                            <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm font-bold text-blue-800">
                                Reviewer mode active: content fields are read-only. You can update workflow status and review notes.
                            </div>
                        <?php endif; ?>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                            <div class="md:col-span-3">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Chapter Title</label>
                                <input type="text" name="chapter_name" value="<?php echo htmlspecialchars($edit_name); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow outline-none" placeholder="e.g. PHP Variables" <?php echo $canEditContent ? '' : 'readonly'; ?> required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Position</label>
                                <input type="number" name="order_index" value="<?php echo $edit_order; ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow outline-none text-center" <?php echo $canEditContent ? '' : 'readonly'; ?> required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Workflow Status</label>
                                <select name="editorial_status" class="form-input">
                                    <?php
                                    $statusOptions = ['draft' => 'Draft', 'in_review' => 'In Review', 'published' => 'Published'];
                                    foreach ($statusOptions as $statusKey => $statusLabel):
                                        $disabled = ($adminRole === 'editor' && $statusKey === 'published') ? 'disabled' : '';
                                    ?>
                                        <option value="<?php echo h($statusKey); ?>" <?php echo $edit_editorial_status === $statusKey ? 'selected' : ''; ?> <?php echo $disabled; ?>><?php echo h($statusLabel); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if($adminRole === 'editor'): ?><p class="text-xs text-amber-700 mt-1 font-bold">Editors can move chapters to In Review. Reviewers/Owners publish.</p><?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Review Notes</label>
                                <textarea name="review_notes" rows="3" class="form-input text-sm" placeholder="Reviewer comments, pending fixes, approval notes..."><?php echo h($edit_review_notes); ?></textarea>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Tutorial Content</label>
                            <div class="mb-3 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-900">
                                <strong>Auto format tips:</strong> Use <code>## Heading</code>, <code>**bold text**</code>, numbered lines like <code>1. Point</code>, bullet lines like <code>* Point</code>, and <code>[IMAGE: describe image]</code>. The public chapter page will convert it into professional tutorial formatting automatically.
                            </div>
                            <textarea id="content" name="content"><?php echo htmlspecialchars($edit_content); ?></textarea>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-3">
                                <div class="editor-metric"><div class="text-xs font-black text-slate-400 uppercase">Words</div><div id="metricWords" class="text-lg font-black text-slate-900 mt-1">0</div></div>
                                <div class="editor-metric"><div class="text-xs font-black text-slate-400 uppercase">Characters</div><div id="metricChars" class="text-lg font-black text-slate-900 mt-1">0</div></div>
                                <div class="editor-metric"><div class="text-xs font-black text-slate-400 uppercase">Headings</div><div id="metricHeadings" class="text-lg font-black text-blue-700 mt-1">0</div></div>
                                <div class="editor-metric"><div class="text-xs font-black text-slate-400 uppercase">Read Time</div><div id="metricReadTime" class="text-lg font-black text-emerald-700 mt-1">0 min</div></div>
                            </div>
                        </div>

                        <?php if($canEditContent): ?>
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
                                    <?php if($canEditContent): ?><button type="button" id="addQuizQuestion" class="rounded-lg bg-slate-900 hover:bg-blue-600 text-white px-4 py-2 text-sm font-bold">Add Question</button><?php endif; ?>
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
                        <?php endif; ?>

                        <?php if($canEditContent): ?>
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
                        <?php endif; ?>

                        <?php if($canEditContent): ?>
                        <div class="mb-8 rounded-xl border border-gray-200 p-5">
                            <h4 class="font-black text-slate-900 mb-3">Practice Set</h4>
                            <textarea name="practice_content" rows="6" class="form-input text-sm" placeholder="Add practice tasks, exercises, project prompt, or homework for this chapter."><?php echo h($edit_practice_content); ?></textarea>
                        </div>
                        <?php endif; ?>

                        <div class="mb-8 rounded-xl border border-indigo-200 bg-indigo-50 p-5">
                            <h4 class="font-black text-indigo-900 mb-3">Editor Assistant</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                <button type="button" id="insertTemplateOutline" class="text-left rounded-lg bg-white border border-indigo-100 px-3 py-2 font-bold text-indigo-700 hover:bg-indigo-100">Insert lesson outline template</button>
                                <button type="button" id="insertTemplateCode" class="text-left rounded-lg bg-white border border-indigo-100 px-3 py-2 font-bold text-indigo-700 hover:bg-indigo-100">Insert code explanation template</button>
                                <button type="button" id="insertTemplateImage" class="text-left rounded-lg bg-white border border-indigo-100 px-3 py-2 font-bold text-indigo-700 hover:bg-indigo-100">Insert image placeholder</button>
                                <button type="button" id="clearLocalDraft" class="text-left rounded-lg bg-white border border-rose-100 px-3 py-2 font-bold text-rose-700 hover:bg-rose-100">Clear saved local draft</button>
                            </div>
                            <p id="draftState" class="text-xs font-bold text-indigo-700 mt-3">Draft sync: idle</p>
                        </div>

                        <div class="flex items-center">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all focus:outline-none focus:ring-4 focus:ring-blue-300">
                                <?php echo !$canEditContent && $canReviewContent ? 'Update Review Status' : ($edit_id ? 'Update Chapter Content' : 'Publish New Chapter'); ?>
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
                                                <div class="min-w-0">
                                                <p class="text-sm font-medium text-slate-800 truncate" title="<?php echo htmlspecialchars($chapter['chapter_name']); ?>">
                                                    <?php echo htmlspecialchars($chapter['chapter_name']); ?>
                                                </p>
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    <?php if ((int)$chapter['has_quiz'] === 1): ?><span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-100 font-black">Quiz</span><?php endif; ?>
                                                    <?php if ((int)$chapter['has_practice'] === 1): ?><span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-100 font-black">Practice</span><?php endif; ?>
                                                    <?php if ((int)$chapter['has_download'] === 1): ?><span class="text-[10px] px-1.5 py-0.5 rounded bg-violet-50 text-violet-700 border border-violet-100 font-black">Download</span><?php endif; ?>
                                                    <?php if ((int)$chapter['is_premium'] === 1): ?><span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-100 font-black">Premium</span><?php endif; ?>
                                                    <?php if (($chapter['editorial_status'] ?? 'draft') === 'draft'): ?><span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-50 text-slate-700 border border-slate-100 font-black">Draft</span><?php endif; ?>
                                                    <?php if (($chapter['editorial_status'] ?? 'draft') === 'in_review'): ?><span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-100 font-black">In Review</span><?php endif; ?>
                                                    <?php if (($chapter['editorial_status'] ?? 'draft') === 'published'): ?><span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-100 font-black">Published</span><?php endif; ?>
                                                </div>
                                                </div>
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
          const canEditContent = <?php echo $canEditContent ? 'true' : 'false'; ?>;
          if (!canEditContent) {
              $('#content').summernote('disable');
          }

          const chapterNameInput = document.querySelector('input[name="chapter_name"]');
          const orderInput = document.querySelector('input[name="order_index"]');
          const practiceInput = document.querySelector('textarea[name="practice_content"]');
          const isPremiumInput = document.querySelector('input[name="is_premium"]');
          const quizRequiredInput = document.querySelector('input[name="require_quiz_pass"]');
          const draftState = document.getElementById('draftState');
          const draftKey = `chapter-editor-draft-<?php echo (int)$course_id; ?>-<?php echo $edit_id ? (int)$edit_id : 'new'; ?>`;
          let hasUnsavedChanges = false;

          const quizBuilder = document.getElementById('quizBuilder');
          const addQuizQuestion = document.getElementById('addQuizQuestion');
          const quizLines = document.getElementById('quizLines');
          const chapterForm = document.querySelector('form[action^="chapters"]');
          const metricWords = document.getElementById('metricWords');
          const metricChars = document.getElementById('metricChars');
          const metricHeadings = document.getElementById('metricHeadings');
          const metricReadTime = document.getElementById('metricReadTime');

          function computeEditorMetrics() {
              const html = $('#content').summernote('code') || '';
              const text = $('<div>').html(html).text().replace(/\s+/g, ' ').trim();
              const words = text ? text.split(' ').length : 0;
              const chars = text.length;
              const headings = (html.match(/<h[1-6][^>]*>/gi) || []).length;
              metricWords.textContent = String(words);
              metricChars.textContent = String(chars);
              metricHeadings.textContent = String(headings);
              metricReadTime.textContent = `${Math.max(1, Math.ceil(words / 180))} min`;
          }

          function serializeDraft() {
              const quizCards = [];
              quizBuilder.querySelectorAll('[data-quiz-card]').forEach((card) => {
                  quizCards.push({
                      question: card.querySelector('[data-quiz-question]')?.value || '',
                      correct: card.querySelector('[data-quiz-correct]')?.value || '',
                      wrong: Array.from(card.querySelectorAll('[data-quiz-wrong]')).map((i) => i.value || ''),
                  });
              });
              return {
                  chapter_name: chapterNameInput?.value || '',
                  order_index: orderInput?.value || '',
                  content: $('#content').summernote('code'),
                  practice_content: practiceInput?.value || '',
                  is_premium: !!isPremiumInput?.checked,
                  require_quiz_pass: !!quizRequiredInput?.checked,
                  quiz_cards: quizCards,
                  saved_at: new Date().toISOString(),
              };
          }

          function saveLocalDraft() {
              try {
                  localStorage.setItem(draftKey, JSON.stringify(serializeDraft()));
                  draftState.textContent = `Draft sync: saved ${new Date().toLocaleTimeString()}`;
              } catch (e) {
                  draftState.textContent = 'Draft sync: failed';
              }
          }

          function applyDraft(data) {
              if (!data) return;
              if (chapterNameInput) chapterNameInput.value = data.chapter_name || chapterNameInput.value;
              if (orderInput) orderInput.value = data.order_index || orderInput.value;
              if (practiceInput) practiceInput.value = data.practice_content || practiceInput.value;
              if (isPremiumInput) isPremiumInput.checked = !!data.is_premium;
              if (quizRequiredInput) quizRequiredInput.checked = !!data.require_quiz_pass;
              if (data.content) $('#content').summernote('code', data.content);
              if (Array.isArray(data.quiz_cards) && data.quiz_cards.length >= 10) {
                  quizBuilder.innerHTML = '';
                  data.quiz_cards.slice(0, 25).forEach((item) => {
                      const card = createQuizCard();
                      card.querySelector('[data-quiz-question]').value = item.question || '';
                      card.querySelector('[data-quiz-correct]').value = item.correct || '';
                      const wrongInputs = card.querySelectorAll('[data-quiz-wrong]');
                      (item.wrong || []).slice(0, 3).forEach((value, idx) => {
                          if (wrongInputs[idx]) wrongInputs[idx].value = value || '';
                      });
                      quizBuilder.appendChild(card);
                  });
              }
              renumberQuizCards();
              computeEditorMetrics();
          }

          function markUnsaved() {
              hasUnsavedChanges = true;
              draftState.textContent = 'Draft sync: unsaved changes';
          }

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
          quizBuilder?.addEventListener('input', markUnsaved);
          chapterNameInput?.addEventListener('input', markUnsaved);
          orderInput?.addEventListener('input', markUnsaved);
          practiceInput?.addEventListener('input', markUnsaved);
          isPremiumInput?.addEventListener('change', markUnsaved);
          quizRequiredInput?.addEventListener('change', markUnsaved);

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
              hasUnsavedChanges = false;
              localStorage.removeItem(draftKey);
          });

          $('#content').on('summernote.change', function () {
              markUnsaved();
              computeEditorMetrics();
          });

          setInterval(() => {
              if (hasUnsavedChanges) {
                  saveLocalDraft();
                  hasUnsavedChanges = false;
              }
          }, 15000);

          window.addEventListener('beforeunload', function (event) {
              if (!hasUnsavedChanges) return;
              event.preventDefault();
              event.returnValue = '';
          });

          const existingDraft = localStorage.getItem(draftKey);
          if (existingDraft) {
              try {
                  const parsed = JSON.parse(existingDraft);
                  if (confirm('A local draft was found for this chapter editor. Restore it?')) {
                      applyDraft(parsed);
                      draftState.textContent = `Draft sync: restored (${new Date(parsed.saved_at || Date.now()).toLocaleString()})`;
                  }
              } catch (e) {}
          }

          document.getElementById('clearLocalDraft')?.addEventListener('click', () => {
              localStorage.removeItem(draftKey);
              draftState.textContent = 'Draft sync: cleared';
          });

          document.getElementById('insertTemplateOutline')?.addEventListener('click', () => {
              $('#content').summernote('pasteHTML', '<h3>## Learning Objective</h3><p>Explain what this lesson covers.</p><h3>## Concept Breakdown</h3><p>Step-by-step explanation.</p><h3>## Summary</h3><p>Quick recap points.</p>');
          });
          document.getElementById('insertTemplateCode')?.addEventListener('click', () => {
              $('#content').summernote('pasteHTML', '<h3>## Code Example</h3><p>```language<br>// sample code<br>```</p><h3>## Explanation</h3><p>Explain line by line.</p>');
          });
          document.getElementById('insertTemplateImage')?.addEventListener('click', () => {
              $('#content').summernote('pasteHTML', '<p>[IMAGE: Add screenshot explaining this section]</p>');
          });

          computeEditorMetrics();
          renumberQuizCards();
      });
    </script>
</body>
</html>
