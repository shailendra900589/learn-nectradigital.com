<?php
require_once 'includes/db.php';

$studentId = (int)($_GET['u'] ?? $_POST['u'] ?? 0);
$token = (string)($_GET['t'] ?? $_POST['t'] ?? '');
$valid = $studentId > 0 && hash_equals(email_unsubscribe_token($studentId), $token);

if ($valid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare("UPDATE students SET marketing_opt_out = 1 WHERE id = :id")->execute(['id' => $studentId]);
    if (str_contains((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/x-www-form-urlencoded') && ($_POST['List-Unsubscribe'] ?? '') === 'One-Click') {
        http_response_code(200);
        exit('Unsubscribed');
    }
    $done = true;
}

$seo_title = 'Email preferences';
$seo_robots = 'noindex, nofollow';
require_once 'includes/header.php';
?>
<section class="max-w-xl mx-auto px-4 py-16">
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
        <?php if (!$valid): ?>
            <h1 class="text-2xl font-black text-slate-900"><?php echo h(localize_text('This link is not valid.')); ?></h1>
        <?php elseif (!empty($done)): ?>
            <h1 class="text-2xl font-black text-slate-900"><?php echo h(localize_text('You are unsubscribed')); ?></h1>
            <p class="text-slate-500 mt-2"><?php echo h(localize_text('You will no longer receive promotional emails. Account emails such as receipts and verification codes will still arrive.')); ?></p>
        <?php else: ?>
            <h1 class="text-2xl font-black text-slate-900"><?php echo h(localize_text('Stop promotional emails?')); ?></h1>
            <p class="text-slate-500 mt-2"><?php echo h(localize_text('You will still receive account emails such as receipts and verification codes.')); ?></p>
            <form method="POST" action="unsubscribe" class="mt-6">
                <input type="hidden" name="u" value="<?php echo (int)$studentId; ?>">
                <input type="hidden" name="t" value="<?php echo h($token); ?>">
                <button class="edu-primary-btn px-6 py-3"><?php echo h(localize_text('Unsubscribe')); ?></button>
            </form>
        <?php endif; ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
