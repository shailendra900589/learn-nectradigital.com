<?php
require_once 'includes/db.php';
secure_session_start();

$seo_title = localize_text('Privacy Policy');
$seo_description = localize_text('Learn how Learn.Nectra protects student accounts, public profiles, payments, and uploaded profile images.');
$seo_canonical = absolute_url('privacy');
require_once 'includes/header.php';
ob_start();
?>

<section class="page-intro text-white py-12 md:py-16 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background: radial-gradient(circle at 50% 20%, #815bc4 0%, transparent 60%);"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <span class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Trust &amp; Transparency
        </span>
        <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight">Privacy Policy</h1>
        <p class="text-slate-300 mt-2 text-sm">Last updated: <?php echo date('F j, Y'); ?></p>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-8 text-slate-600 leading-relaxed text-sm sm:text-base">
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">What We Collect</h2>
            <p>We collect account details such as name, email, password hash, learning progress, profile fields you choose to add, subscription records, and payment references you submit for admin verification.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Public Profiles</h2>
            <p>Your public learning profile is controlled from your profile settings. Email and phone are hidden by default and only appear publicly when you enable those options.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Payments & Plans</h2>
            <p>Payment history is visible to you and administrators. We store payment references for verification, but this site does not ask for full card numbers or sensitive banking secrets.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Uploads & Security</h2>
            <p>Profile images are validated by file type and size. Uploaded files are not allowed to execute as server code, and account actions use protected sessions and security tokens.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Your Choices</h2>
            <p>You can update your profile, hide your public profile, hide contact fields, change your password, and review payment history from your account pages. Contact support for account deletion or data export requests.</p>
        </div>
    </div>
</section>

<?php
echo localize_lesson_html(ob_get_clean());
require_once 'includes/footer.php';
?>
