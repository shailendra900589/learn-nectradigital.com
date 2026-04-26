<?php
require_once 'includes/db.php';
secure_session_start();

$seo_title = 'Terms of Service';
$seo_description = 'Learn.Nectra terms for student accounts, courses, payments, subscriptions, and acceptable use.';
$seo_canonical = absolute_url('terms');
require_once 'includes/header.php';
?>

<section class="site-hero-light py-12 border-b border-blue-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="edu-pill mb-4">Platform Rules</span>
        <h1 class="text-4xl md:text-5xl font-black text-slate-950">Terms of Service</h1>
        <p class="text-slate-600 mt-3">Last updated: <?php echo date('F j, Y'); ?></p>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6 md:p-8 space-y-7 text-slate-600 leading-8">
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Accounts</h2>
            <p>You are responsible for keeping your login secure and for the content you add to your profile. Admin may pause or block access when abuse, fraud, or security risk is detected.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Learning Content</h2>
            <p>Courses are provided for learning and reference. You may not copy, resell, scrape, or misuse platform content without written permission.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Plans & Payments</h2>
            <p>Paid plans activate after admin verification. Admin can enable, pause, reject, refund, or disable plans based on payment status, account status, or platform policy.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Public Profiles</h2>
            <p>You control whether your profile is public. Keep profile information professional and avoid uploading private, illegal, or sensitive material.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Changes</h2>
            <p>We may update these terms to improve security, payments, learning access, or legal compliance. Continued use means you accept the latest version.</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
