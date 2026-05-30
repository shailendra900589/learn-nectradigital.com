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
            <h2 class="text-2xl font-black text-slate-900 mb-2">1) Information We Collect</h2>
            <p>We may collect account and profile data (name, email, password hash, optional profile fields), learning activity (course progress, quiz attempts), subscription/payment metadata, notification preferences, and technical logs required for security and operations.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">2) How We Use Data</h2>
            <p>Data is used to operate your account, deliver courses, track progress, process plan status, improve recommendations, show relevant ad placements for non-premium users, and secure the platform against fraud or abuse.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">3) Public Profile Controls</h2>
            <p>Your public profile visibility is configurable. Contact fields like email and phone remain private by default and are shown publicly only when explicitly enabled by you.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">4) Payments and Financial Data</h2>
            <p>We store payment references and verification records for plan activation workflows. We do not intentionally store full card credentials or sensitive banking authentication secrets in this learning application.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">5) Ads and Recommendation Systems</h2>
            <p>Non-premium users may receive multi-format ads in lesson content. We may use contextual and activity-based signals to improve fill rate and relevance. Premium/ad-free users may receive reduced or no ad delivery depending on their plan and account settings.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">6) Notifications and Email</h2>
            <p>We may send in-app or email notifications for security events, learning progress, billing updates, and recommendations. Digest frequencies and notification types may be controlled through profile settings where provided.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">7) Data Sharing and Processors</h2>
            <p>We may use external infrastructure/services (such as email or ad providers) to operate platform functionality. We do not sell your personal profile data as a standalone product.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">8) Security Measures</h2>
            <p>We apply session hardening, CSRF controls, upload validation, and server-side checks. No system is perfect, but we continuously improve safeguards against unauthorized access and misuse.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">9) Data Retention</h2>
            <p>We retain information as needed for educational service delivery, compliance, dispute handling, and platform security. Retention periods may vary by data type and legal obligations.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">10) Your Rights and Requests</h2>
            <p>You can update profile information, change password, and adjust privacy/notification preferences from account pages. For deletion, correction, or export requests, contact us through the contact page.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">11) Policy Updates</h2>
            <p>We may update this Privacy Policy to reflect platform changes, legal requirements, or security improvements. Continued usage after updates indicates acceptance of the revised policy.</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
