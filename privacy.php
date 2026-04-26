<?php
require_once 'includes/db.php';
secure_session_start();

$seo_title = 'Privacy Policy';
$seo_description = 'Learn how Learn.Nectra protects student accounts, public profiles, payments, and uploaded profile images.';
$seo_canonical = absolute_url('privacy');
require_once 'includes/header.php';
?>

<section class="site-hero-light py-12 border-b border-blue-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="edu-pill mb-4">Trust & Safety</span>
        <h1 class="text-4xl md:text-5xl font-black text-slate-950">Privacy Policy</h1>
        <p class="text-slate-600 mt-3">Last updated: <?php echo date('F j, Y'); ?></p>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6 md:p-8 space-y-7 text-slate-600 leading-8">
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

<?php require_once 'includes/footer.php'; ?>
