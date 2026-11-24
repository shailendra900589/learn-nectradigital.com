<?php
require_once 'includes/db.php';
secure_session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    unset($_SESSION['student_logged_in'], $_SESSION['student_id'], $_SESSION['student_name']);
    session_regenerate_id(true);
    header('Location: ' . localized_url(''));
    exit;
}

if (empty($_SESSION['student_logged_in'])) {
    header('Location: ' . localized_url(''));
    exit;
}

$seo_title = 'Sign out';
$seo_description = 'Sign out of your Learn.Nectra account.';
$seo_robots = 'noindex, nofollow';
require_once 'includes/header.php';
?>
<main id="main-content" class="max-w-lg mx-auto px-4 py-16">
    <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
        <h1 class="text-2xl font-black text-slate-900 mb-2">Sign out</h1>
        <p class="text-slate-600 mb-6">This ends your student session on this browser.</p>
        <form method="post" action="<?php echo h(app_path('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="edu-primary-btn px-6 py-3">Sign out</button>
        </form>
    </section>
</main>
<?php require_once 'includes/footer.php'; ?>
