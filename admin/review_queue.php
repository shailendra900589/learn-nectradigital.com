<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();
require_admin_role($pdo, ['owner', 'reviewer']);

$error = '';
$success = '';
$adminId = (int)($_SESSION['admin_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $chapterId = post_int('chapter_id');
    $notes = substr(trim($_POST['review_notes'] ?? ''), 0, 2000);

    $stmt = $pdo->prepare("
        SELECT ch.id, ch.chapter_name, ch.editorial_status, c.id AS course_id, c.slug AS course_slug
        FROM chapters ch
        INNER JOIN courses c ON c.id = ch.course_id
        WHERE ch.id = :id
        LIMIT 1
    ");
    $stmt->execute(['id' => $chapterId]);
    $chapter = $stmt->fetch();

    if (!$chapter) {
        $error = 'Chapter not found.';
    } else {
        if ($action === 'approve_publish') {
            $update = $pdo->prepare("
                UPDATE chapters
                SET editorial_status = 'published',
                    review_notes = :review_notes,
                    reviewed_by_admin_id = :reviewed_by,
                    reviewed_at = NOW(),
                    published_at = NOW()
                WHERE id = :id
            ");
            $update->execute([
                'review_notes' => $notes,
                'reviewed_by' => $adminId,
                'id' => $chapterId,
            ]);
            $success = 'Chapter approved and published.';
        } elseif ($action === 'send_back_draft') {
            $update = $pdo->prepare("
                UPDATE chapters
                SET editorial_status = 'draft',
                    review_notes = :review_notes,
                    reviewed_by_admin_id = :reviewed_by,
                    reviewed_at = NOW()
                WHERE id = :id
            ");
            $update->execute([
                'review_notes' => $notes,
                'reviewed_by' => $adminId,
                'id' => $chapterId,
            ]);
            $success = 'Chapter sent back to draft.';
        }
    }
}

$queueItems = $pdo->query("
    SELECT ch.id, ch.chapter_name, ch.order_index, ch.review_notes, ch.created_at, ch.updated_by_admin_id,
           c.id AS course_id, c.title AS course_title, c.slug AS course_slug,
           u.username AS editor_username
    FROM chapters ch
    INNER JOIN courses c ON c.id = ch.course_id
    LEFT JOIN users u ON u.id = ch.updated_by_admin_id
    WHERE ch.editorial_status = 'in_review'
    ORDER BY ch.id ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Queue | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo admin_styles(); ?>
</head>
<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden text-slate-800">
    <?php echo admin_sidebar($pdo, 'review_queue'); ?>

    <main class="flex-1 overflow-y-auto">
        <header class="admin-header flex items-center justify-between px-8 sticky top-0 z-10">
            <div>
                <div class="text-sm font-black text-blue-600 uppercase tracking-widest">Editorial Workflow</div>
                <h2 class="text-2xl font-black text-slate-950">Review Queue</h2>
            </div>
            <span class="text-sm font-bold text-slate-500"><?php echo count($queueItems); ?> chapter(s) waiting</span>
        </header>

        <div class="p-8 space-y-6">
            <?php if($error): ?><div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md"><?php echo h($error); ?></div><?php endif; ?>
            <?php if($success): ?><div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md"><?php echo h($success); ?></div><?php endif; ?>

            <?php if (!$queueItems): ?>
                <div class="admin-card p-10 text-center">
                    <h3 class="text-xl font-black text-slate-900">Review queue is clear</h3>
                    <p class="text-slate-500 mt-2">No chapters are currently in review.</p>
                </div>
            <?php endif; ?>

            <?php foreach($queueItems as $item): ?>
                <article class="admin-card p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div>
                            <div class="text-xs font-black uppercase tracking-widest text-blue-600">In Review</div>
                            <h3 class="text-xl font-black text-slate-900 mt-1"><?php echo h($item['chapter_name']); ?></h3>
                            <p class="text-sm text-slate-500 mt-1"><?php echo h($item['course_title']); ?> - Lesson <?php echo (int)$item['order_index']; ?></p>
                            <p class="text-xs text-slate-400 mt-2">Submitted by: <?php echo h($item['editor_username'] ?: 'Unknown editor'); ?></p>
                            <?php if (!empty($item['review_notes'])): ?>
                                <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                                    <span class="font-black text-slate-700">Current notes:</span> <?php echo h($item['review_notes']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="chapters?course_id=<?php echo (int)$item['course_id']; ?>&edit=<?php echo (int)$item['id']; ?>" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 font-black hover:bg-slate-50">Open Editor</a>
                            <a href="../<?php echo h(chapter_path((string)$item['course_slug'], (int)$item['id'], (string)$item['chapter_name'])); ?>" target="_blank" class="px-4 py-2 rounded-lg border border-blue-200 text-blue-700 font-black hover:bg-blue-50">Preview URL</a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-5">
                        <form method="POST" action="review_queue" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="approve_publish">
                            <input type="hidden" name="chapter_id" value="<?php echo (int)$item['id']; ?>">
                            <label class="block text-xs font-black uppercase tracking-widest text-emerald-700 mb-2">Approval notes</label>
                            <textarea name="review_notes" rows="3" class="w-full border border-emerald-200 rounded-lg p-3 text-sm bg-white" placeholder="Approved. Optional notes for audit trail."><?php echo h((string)$item['review_notes']); ?></textarea>
                            <button class="mt-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black px-4 py-2 rounded-lg">Approve & Publish</button>
                        </form>

                        <form method="POST" action="review_queue" class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="send_back_draft">
                            <input type="hidden" name="chapter_id" value="<?php echo (int)$item['id']; ?>">
                            <label class="block text-xs font-black uppercase tracking-widest text-amber-700 mb-2">Revision notes</label>
                            <textarea name="review_notes" rows="3" class="w-full border border-amber-200 rounded-lg p-3 text-sm bg-white" placeholder="Explain what needs to be improved before publish." required></textarea>
                            <button class="mt-3 bg-amber-600 hover:bg-amber-700 text-white font-black px-4 py-2 rounded-lg">Send Back to Draft</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
