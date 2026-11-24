<?php
require_once 'includes/db.php';
secure_session_start();

if (empty($_SESSION['student_logged_in'])) {
    redirect(app_path('login'));
}

$studentId = (int)$_SESSION['student_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create_order') {
        enforce_request_budget('billing-order', 20, 3600);
        if (!plans_enabled($pdo) || !razorpay_enabled()) {
            json_response(['status' => 'error', 'message' => localize_text('Online payment is not available right now. Please contact support.')], 503);
        }
        $stmtPlan = $pdo->prepare("SELECT * FROM plans WHERE id = :id AND is_active = 1 LIMIT 1");
        $stmtPlan->execute(['id' => post_int('plan_id')]);
        $plan = $stmtPlan->fetch();
        if (!$plan) {
            json_response(['status' => 'error', 'message' => localize_text('Selected plan is not available.')], 400);
        }
        $courseId = null;
        if (!plan_is_all_access($plan) && (int)$plan['course_limit'] === 1) {
            $courseId = post_int('course_id');
            $stmtCourse = $pdo->prepare("SELECT id FROM courses WHERE id = :id LIMIT 1");
            $stmtCourse->execute(['id' => $courseId]);
            if (!$stmtCourse->fetchColumn()) {
                json_response(['status' => 'error', 'message' => localize_text('Please choose a course first.')], 400);
            }
            if (student_course_access($pdo, $studentId, $courseId)) {
                json_response(['status' => 'error', 'message' => localize_text('You already have this course in an active package.')], 400);
            }
        }
        $amountPaise = (int)round(plan_effective_price($plan) * 100);
        if ($amountPaise < 100) {
            json_response(['status' => 'error', 'message' => localize_text('Selected plan is not available.')], 400);
        }
        $order = razorpay_create_order($amountPaise, 'student-' . $studentId . '-' . time(), [
            'student_id' => (string)$studentId,
            'plan' => (string)$plan['slug'],
            'course_id' => (string)($courseId ?? ''),
        ]);
        if (!$order) {
            json_response(['status' => 'error', 'message' => localize_text('Payment could not be started. Please try again in a minute.')], 502);
        }
        $pdo->prepare("
            INSERT INTO payments (student_id, plan_id, course_id, amount, currency, method, gateway_order_id, status)
            VALUES (:student_id, :plan_id, :course_id, :amount, 'INR', 'razorpay', :order_id, 'requested')
        ")->execute([
            'student_id' => $studentId,
            'plan_id' => (int)$plan['id'],
            'course_id' => $courseId,
            'amount' => plan_effective_price($plan),
            'order_id' => $order['id'],
        ]);
        $stmtStudent = $pdo->prepare("SELECT name, email, phone FROM students WHERE id = :id LIMIT 1");
        $stmtStudent->execute(['id' => $studentId]);
        $student = $stmtStudent->fetch() ?: [];
        json_response([
            'status' => 'success',
            'key' => razorpay_key_id(),
            'order_id' => $order['id'],
            'amount' => $amountPaise,
            'currency' => 'INR',
            'name' => 'Learn.Nectra',
            'description' => (string)$plan['name'],
            'prefill' => [
                'name' => (string)($student['name'] ?? ''),
                'email' => (string)($student['email'] ?? ''),
                'contact' => (string)($student['phone'] ?? ''),
            ],
        ]);
    }

    if ($action === 'verify_payment') {
        $orderId = substr(trim((string)($_POST['razorpay_order_id'] ?? '')), 0, 64);
        $paymentId = substr(trim((string)($_POST['razorpay_payment_id'] ?? '')), 0, 64);
        $signature = trim((string)($_POST['razorpay_signature'] ?? ''));
        $stmt = $pdo->prepare("SELECT id FROM payments WHERE gateway_order_id = :order_id AND student_id = :student_id LIMIT 1");
        $stmt->execute(['order_id' => $orderId, 'student_id' => $studentId]);
        if (!$stmt->fetchColumn() || !razorpay_payment_signature_valid($orderId, $paymentId, $signature)) {
            json_response(['status' => 'error', 'message' => localize_text('Payment could not be verified. If money was deducted, contact support with your payment ID.')], 400);
        }
        if (!complete_gateway_payment($pdo, $orderId, $paymentId)) {
            json_response(['status' => 'error', 'message' => localize_text('Payment received but activation failed. Contact support with your payment ID.')], 500);
        }
        json_response(['status' => 'success']);
    }

    if ($action === 'order_status') {
        enforce_request_budget('billing-status', 150, 900);
        $orderId = substr(trim((string)($_POST['order_id'] ?? '')), 0, 64);
        $stmt = $pdo->prepare("SELECT status FROM payments WHERE gateway_order_id = :order_id AND student_id = :student_id LIMIT 1");
        $stmt->execute(['order_id' => $orderId, 'student_id' => $studentId]);
        $current = $stmt->fetchColumn();
        if ($current === false) {
            json_response(['status' => 'error', 'message' => localize_text('Selected plan is not available.')], 404);
        }
        if ($current === 'approved') {
            json_response(['status' => 'success', 'paid' => true]);
        }
        $paymentId = razorpay_captured_payment_id($orderId);
        $paid = $paymentId !== null && complete_gateway_payment($pdo, $orderId, $paymentId);
        json_response(['status' => 'success', 'paid' => $paid]);
    }

    if ($action === 'claim_course') {
        $courseId = post_int('course_id');
        $result = claim_course_slot($pdo, $studentId, $courseId);
        $back = (string)($_POST['return'] ?? '');
        if ($result === 'added' || $result === 'already') {
            if ($back !== '' && preg_match('#^course/[a-z0-9-]+$#', $back)) {
                redirect(localized_url($back));
            }
            $success = $result === 'added' ? 'Course added to your package. It is ad-free now.' : 'This course is already in your package.';
        } else {
            $error = $result === 'no_slot' ? 'Your package has no free course slots left.' : 'Please choose a course first.';
        }
    }
}

$packages = active_subscriptions($pdo, $studentId);
$plans = plans_enabled($pdo) ? $pdo->query("SELECT * FROM plans WHERE is_active = 1 ORDER BY price ASC, id ASC")->fetchAll() : [];
$courses = $pdo->query("SELECT id, title FROM courses ORDER BY title ASC")->fetchAll();
$preselectCourse = get_int('course');
$freeSlots = student_free_course_slots($pdo, $studentId);
$gatewayReady = razorpay_enabled();

$stmtPayments = $pdo->prepare("
    SELECT pay.*, p.name AS plan_name
    FROM payments pay
    LEFT JOIN plans p ON p.id = pay.plan_id
    WHERE pay.student_id = :student_id AND pay.status <> 'requested'
    ORDER BY pay.created_at DESC
");
$stmtPayments->execute(['student_id' => $studentId]);
$payments = $stmtPayments->fetchAll();

$billingCopy = [
    'Plans and Payment History',
    'Manage your Learn.Nectra plan, ad-free access, and payment history.',
    'Course Packages',
    'Choose your package',
    'Every package lasts a full year. Lessons stay free for everyone; packages remove ads and unlock downloads and certificates for your courses.',
    'Plans are currently disabled',
    'Admin has paused plan purchase requests for now.',
    'My Packages',
    'Free Account',
    'Ads may appear on free accounts.',
    'All courses',
    'Valid until',
    'Course slots used',
    'Choose a course',
    'Add course',
    'Payment History',
    'Payment',
    'No payments yet.',
    'Pay now',
    'Course for this package',
    'Pick your courses after payment.',
    '1 year',
    'Online payment is being set up. Please contact support to buy a package.',
    'Contact support',
    'Opening secure payment...',
    'Payment successful. Activating your package...',
    'Payment was cancelled.',
    'Ad-free sitewide',
    'Choose the remaining courses for your package.',
    'Secure payment by Razorpay: UPI, cards, net banking, and wallets.',
    'approved',
    'rejected',
    'refunded',
    'paid',
];
foreach ([$error, $success] as $message) {
    if ($message !== '') {
        $billingCopy[] = $message;
    }
}
$billingCopy[] = 'Offer ends';
$billingCopy[] = 'Limited offer';
foreach ($plans as $plan) {
    if (!empty($plan['offer_label'])) {
        $billingCopy[] = (string)$plan['offer_label'];
    }
    $billingCopy[] = (string)$plan['name'];
    $billingCopy[] = (string)$plan['description'];
    foreach (plan_features($plan['features'] ?? '') as $feature) {
        $billingCopy[] = (string)$feature;
    }
}
foreach ($packages as $package) {
    $billingCopy[] = (string)$package['plan_name'];
}
foreach ($payments as $payment) {
    if (!empty($payment['plan_name'])) {
        $billingCopy[] = (string)$payment['plan_name'];
    }
}
if (current_lang() !== 'en') {
    localize_texts($billingCopy);
}
$seo_title = localize_text('Plans and Payment History');
$seo_description = localize_text('Manage your Learn.Nectra plan, ad-free access, and payment history.');
$seo_robots = 'noindex, nofollow';
$billingBoot = json_encode([
    'endpoint' => lang_href('billing'),
    'csrf' => csrf_token(),
    'text' => [
        'opening' => localize_text('Opening secure payment...'),
        'success' => localize_text('Payment successful. Activating your package...'),
        'cancelled' => localize_text('Payment was cancelled.'),
        'failed' => localize_text('Payment failed:'),
        'choose' => localize_text('Please choose a course first.'),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
require_once 'includes/header.php';
?>

<section class="page-intro text-white py-12 md:py-16 border-b border-slate-800 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <span class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4">
            <?php echo h(localize_text('Course Packages')); ?>
        </span>
        <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight"><?php echo h(localize_text('Choose your package')); ?></h1>
        <p class="text-slate-300 mt-2 max-w-2xl text-sm sm:text-base"><?php echo h(localize_text('Every package lasts a full year. Lessons stay free for everyone; packages remove ads and unlock downloads and certificates for your courses.')); ?></p>
    </div>
</section>

<section id="billing-app" data-billing="<?php echo h($billingBoot); ?>" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div id="billing-alert" class="mb-6 rounded-md p-4 border-l-4 <?php echo $error ? 'bg-red-50 border-red-500 text-red-700' : 'bg-green-50 border-green-500 text-green-700'; ?>" role="status" <?php echo ($error || $success) ? '' : 'hidden'; ?>><?php echo h(localize_text($error ?: $success)); ?></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <div class="lg:col-span-2">
            <?php if (!$plans): ?>
                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                    <h2 class="text-2xl font-black text-slate-900"><?php echo h(localize_text('Plans are currently disabled')); ?></h2>
                    <p class="text-slate-500 mt-2"><?php echo h(localize_text('Admin has paused plan purchase requests for now.')); ?></p>
                </div>
            <?php else: ?>
                <?php if (!$gatewayReady): ?>
                    <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        <?php echo h(localize_text('Online payment is being set up. Please contact support to buy a package.')); ?>
                        <a class="font-bold underline" href="<?php echo h(lang_href('contact')); ?>"><?php echo h(localize_text('Contact support')); ?></a>
                    </div>
                <?php endif; ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <?php foreach ($plans as $plan): $allAccess = plan_is_all_access($plan); $single = !$allAccess && (int)$plan['course_limit'] === 1; ?>
                        <article class="bg-white rounded-xl border <?php echo $allAccess ? 'border-emerald-400 ring-2 ring-emerald-100' : 'border-gray-200'; ?> learning-surface p-6 flex flex-col">
                            <?php $onOffer = plan_offer_active($plan); ?>
                            <?php if ($onOffer): ?>
                                <span class="self-start mb-2 inline-flex bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full"><?php echo h(localize_text((string)($plan['offer_label'] ?: 'Limited offer'))); ?></span>
                            <?php endif; ?>
                            <h2 class="text-xl font-black text-slate-900"><?php echo h(localize_text((string)$plan['name'])); ?></h2>
                            <div class="mt-3 flex items-baseline gap-2">
                                <?php if ($onOffer): ?><s class="text-base font-bold text-slate-400">&#8377;<?php echo h(format_rupees((float)$plan['price'])); ?></s><?php endif; ?>
                                <span class="text-3xl font-black text-slate-900">&#8377;<?php echo h(format_rupees(plan_effective_price($plan))); ?></span>
                                <span class="text-xs font-bold text-slate-400">/ <?php echo h((int)$plan['duration_days'] === 365 ? localize_text('1 year') : (int)$plan['duration_days'] . ' days'); ?></span>
                            </div>
                            <p class="text-sm text-slate-500 mt-3"><?php echo h(localize_text((string)$plan['description'])); ?></p>
                            <ul class="mt-4 space-y-2 text-sm text-slate-700 flex-1">
                                <?php foreach (plan_features($plan['features'] ?? '') as $feature): ?>
                                    <li class="flex gap-2"><span class="text-emerald-600 font-black">&check;</span><span><?php echo h(localize_text((string)$feature)); ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="mt-5 space-y-3">
                                <?php if ($single): ?>
                                    <label class="block text-xs font-bold text-slate-600" for="course-for-<?php echo (int)$plan['id']; ?>"><?php echo h(localize_text('Course for this package')); ?></label>
                                    <select id="course-for-<?php echo (int)$plan['id']; ?>" data-course-select class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
                                        <option value=""><?php echo h(localize_text('Choose a course')); ?></option>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?php echo (int)$course['id']; ?>" <?php echo $preselectCourse === (int)$course['id'] ? 'selected' : ''; ?>><?php echo h(localize_text((string)$course['title'])); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif (!$allAccess): ?>
                                    <p class="text-xs text-slate-500"><?php echo h(localize_text('Pick your courses after payment.')); ?></p>
                                <?php endif; ?>
                                <button type="button" data-buy-plan="<?php echo (int)$plan['id']; ?>" class="w-full edu-primary-btn px-5 py-3 justify-center" <?php echo $gatewayReady ? '' : 'disabled'; ?>><?php echo h(localize_text('Pay now')); ?> &#8377;<?php echo h(format_rupees(plan_effective_price($plan))); ?></button>
                                <?php if ($onOffer && !empty($plan['offer_ends_at'])): ?>
                                    <p class="text-[11px] text-rose-600 font-bold text-center"><?php echo h(localize_text('Offer ends')); ?> <?php echo h(date('j M Y', strtotime((string)$plan['offer_ends_at']))); ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <p class="mt-4 text-xs text-slate-500"><?php echo h(localize_text('Secure payment by Razorpay: UPI, cards, net banking, and wallets.')); ?></p>
            <?php endif; ?>
        </div>

        <aside class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                <h2 class="text-xl font-black text-slate-900 mb-4"><?php echo h(localize_text('My Packages')); ?></h2>
                <?php if (!$packages): ?>
                    <div class="rounded-lg bg-slate-50 border border-slate-200 p-4">
                        <p class="font-black text-slate-900"><?php echo h(localize_text('Free Account')); ?></p>
                        <p class="text-sm text-slate-500 mt-1"><?php echo h(localize_text('Ads may appear on free accounts.')); ?></p>
                    </div>
                <?php endif; ?>
                <div class="space-y-4">
                    <?php foreach ($packages as $package): $limit = $package['course_limit']; ?>
                        <div class="rounded-lg bg-emerald-50/60 border border-emerald-200 p-4">
                            <p class="font-black text-emerald-800"><?php echo h(localize_text((string)$package['plan_name'])); ?></p>
                            <?php if (!empty($package['ends_at'])): ?>
                                <p class="text-xs text-slate-500 mt-1"><?php echo h(localize_text('Valid until')); ?> <?php echo h(date('M j, Y', strtotime($package['ends_at']))); ?></p>
                            <?php endif; ?>
                            <?php if ($limit === null): ?>
                                <p class="text-sm text-slate-700 mt-2"><?php echo h(localize_text('All courses')); ?> &middot; <?php echo h(localize_text('Ad-free sitewide')); ?></p>
                            <?php else: ?>
                                <p class="text-xs font-bold text-slate-600 mt-2"><?php echo h(localize_text('Course slots used')); ?>: <?php echo (int)$package['courses_used']; ?>/<?php echo (int)$limit; ?></p>
                                <ul class="mt-2 space-y-1 text-sm">
                                    <?php foreach (subscription_courses($pdo, (int)$package['id']) as $owned): ?>
                                        <li><a class="text-emerald-700 font-bold hover:underline" href="<?php echo h(lang_href(course_path($owned['slug']))); ?>"><?php echo h(localize_text((string)$owned['title'])); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($freeSlots > 0): ?>
                    <form method="POST" action="<?php echo h(lang_href('billing')); ?>" class="mt-4 space-y-2">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="claim_course">
                        <p class="text-xs text-slate-600"><?php echo h(localize_text('Choose the remaining courses for your package.')); ?> (<?php echo (int)$freeSlots; ?>)</p>
                        <select name="course_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
                            <option value=""><?php echo h(localize_text('Choose a course')); ?></option>
                            <?php foreach ($courses as $course): if (student_course_access($pdo, $studentId, (int)$course['id'])) continue; ?>
                                <option value="<?php echo (int)$course['id']; ?>"><?php echo h(localize_text((string)$course['title'])); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="w-full edu-primary-btn px-4 py-2.5 justify-center"><?php echo h(localize_text('Add course')); ?></button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                <h2 class="text-xl font-black text-slate-900 mb-4"><?php echo h(localize_text('Payment History')); ?></h2>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($payments as $payment): ?>
                        <div class="py-3 text-sm">
                            <div class="flex justify-between gap-3">
                                <span class="font-bold text-slate-800"><?php echo h(localize_text((string)($payment['plan_name'] ?: 'Payment'))); ?></span>
                                <span class="font-black text-slate-900">&#8377;<?php echo number_format((float)$payment['amount'], 2); ?></span>
                            </div>
                            <div class="mt-1 flex justify-between gap-3 text-xs text-slate-500">
                                <span><?php echo h((string)($payment['gateway_payment_id'] ?: $payment['transaction_ref'] ?: '')); ?></span>
                                <span class="uppercase font-black"><?php echo h(localize_text((string)$payment['status'])); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$payments): ?><p class="text-sm text-slate-500"><?php echo h(localize_text('No payments yet.')); ?></p><?php endif; ?>
                </div>
            </div>
        </aside>
    </div>
</section>

<script src="<?php echo h(protected_asset_url('assets/js/billing.js')); ?>"></script>
<?php require_once 'includes/footer.php'; ?>
