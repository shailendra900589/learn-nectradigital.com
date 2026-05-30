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
            <h2 class="text-2xl font-black text-slate-900 mb-2">1) Acceptance of Terms</h2>
            <p>By using Learn.Nectra, you agree to these Terms of Service and related platform policies. If you do not agree, please stop using the service.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">2) Eligibility and Accounts</h2>
            <p>You are responsible for your account activity, password confidentiality, and profile information accuracy. We may suspend or terminate accounts involved in abuse, fraud, unauthorized access attempts, or policy violations.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">3) Content Ownership and License</h2>
            <p>All courses, chapter content, quizzes, media, and platform design are owned by Learn.Nectra or its licensors. You receive a limited, non-transferable, personal-use license for learning only. Copying, scraping, republishing, or reselling without written permission is prohibited.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">4) Editorial Workflow and Publishing</h2>
            <p>Platform content may pass through internal Draft, Review, and Published states. Only published content is intended for general public use. Draft and review material may change without prior notice.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">5) Plans, Billing, and Refund Handling</h2>
            <p>Premium features are activated according to plan configuration and payment verification status. Plan activation, rejection, suspension, or refund decisions may be taken by authorized admins based on payment records, compliance, or risk signals.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">6) Ads and Non-Premium Experience</h2>
            <p>Non-premium users may see multiple ad formats (including automated placements) across lesson content. Premium or ad-free eligible users may receive reduced or no ad exposure depending on account settings and active plan status.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">7) Notifications and Communication</h2>
            <p>We may send in-app and email notifications for security, billing, recommendations, and product updates. You can manage notification preferences from your profile where available.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">8) Acceptable Use</h2>
            <p>You must not upload malicious code, attempt unauthorized access, abuse platform APIs, harass users, manipulate payment records, or use automated scraping against protected areas.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">9) Liability and Service Availability</h2>
            <p>The platform is provided on an as-available basis. We may update features, modify flows, or temporarily interrupt service for maintenance, security, legal compliance, or operational reasons.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">10) Policy Updates and Contact</h2>
            <p>We may revise these terms from time to time. Continued use after updates means you accept the revised terms. For legal or policy queries, please contact us through the contact page.</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
