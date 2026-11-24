<?php
require_once 'includes/db.php';
secure_session_start();

$seo_title = localize_text('Terms of Service');
$seo_description = localize_text('Learn.Nectra terms for student accounts, courses, payments, subscriptions, and acceptable use.');
$seo_canonical = absolute_url('terms');
require_once 'includes/header.php';
ob_start();
?>

<section class="page-intro text-white py-12 md:py-16 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background: radial-gradient(circle at 50% 20%, #815bc4 0%, transparent 60%);"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <span class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Platform Guidelines
        </span>
        <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight">Terms of Service</h1>
        <p class="text-slate-300 mt-2 text-sm">Last updated: <?php echo date('F j, Y'); ?></p>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-8 text-slate-600 leading-relaxed text-sm sm:text-base">
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

<?php
echo localize_lesson_html(ob_get_clean());
require_once 'includes/footer.php';
?>
