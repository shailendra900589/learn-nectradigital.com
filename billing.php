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

    if ($action === 'request_plan' && plans_enabled($pdo)) {
        $planId = post_int('plan_id');
        $method = substr(trim($_POST['method'] ?? ''), 0, 80);
        $transactionRef = substr(trim($_POST['transaction_ref'] ?? ''), 0, 160);

        $stmtPlan = $pdo->prepare("SELECT * FROM plans WHERE id = :id AND is_active = 1 LIMIT 1");
        $stmtPlan->execute(['id' => $planId]);
        $plan = $stmtPlan->fetch();

        if (!$plan) {
            $error = 'Selected plan is not available.';
        } elseif ($transactionRef === '') {
            $error = 'Please submit a payment or transaction reference.';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO payments (student_id, plan_id, amount, currency, method, transaction_ref, status, paid_at)
                VALUES (:student_id, :plan_id, :amount, :currency, :method, :transaction_ref, 'paid', NOW())
            ");
            $stmt->execute([
                'student_id' => $studentId,
                'plan_id' => $planId,
                'amount' => $plan['price'],
                'currency' => $plan['currency'],
                'method' => $method,
                'transaction_ref' => $transactionRef,
            ]);
            $success = 'Payment submitted. Admin will verify and activate your plan.';
        }
    }
}

$active = active_subscription($pdo, $studentId);
$plans = [];
if (plans_enabled($pdo)) {
    $plans = $pdo->query("SELECT * FROM plans WHERE is_active = 1 ORDER BY price ASC, id ASC")->fetchAll();
}

$stmtPayments = $pdo->prepare("
    SELECT pay.*, p.name AS plan_name
    FROM payments pay
    LEFT JOIN plans p ON p.id = pay.plan_id
    WHERE pay.student_id = :student_id
    ORDER BY pay.created_at DESC
");
$stmtPayments->execute(['student_id' => $studentId]);
$payments = $stmtPayments->fetchAll();

$billingCopy = [
    'Plans and Payment History',
    'Manage your Learn.Nectra plan, ad-free access, and payment history.',
    'Premium Upgrades',
    'Plans & Payments',
    'Choose an ad-free premium plan, submit your payment reference, and track your subscriptions all in one place.',
    'Plans are currently disabled',
    'Admin has paused plan purchase requests for now.',
    'Current Access',
    'Free Account',
    'Ad-free premium access is active.',
    'Ads may appear on free accounts.',
    'Payment History',
    'Payment',
    'No ref',
    'No payments yet.',
    'Selected plan is not available.',
    'Please submit a payment or transaction reference.',
    'Payment submitted. Admin will verify and activate your plan.',
];
if ($error !== '') {
    $billingCopy[] = $error;
}
if ($success !== '') {
    $billingCopy[] = $success;
}
foreach ($plans as $plan) {
    $billingCopy[] = (string)$plan['name'];
    $billingCopy[] = (string)$plan['description'];
    $billingCopy[] = (int)$plan['duration_days'] . ' days';
    foreach (plan_features($plan['features'] ?? '') as $feature) {
        $billingCopy[] = (string)$feature;
    }
}
if (is_array($active) && !empty($active['plan_name'])) {
    $billingCopy[] = (string)$active['plan_name'];
}
if (is_array($active) && !empty($active['ends_at'])) {
    $billingCopy[] = 'Valid until ' . date('M j, Y', strtotime($active['ends_at']));
}
$billingCopy[] = (string)platform_setting($pdo, 'payment_instructions', 'Submit your payment reference for admin approval.');
foreach ($payments as $payment) {
    if (!empty($payment['plan_name'])) {
        $billingCopy[] = (string)$payment['plan_name'];
    }
    if (!empty($payment['status'])) {
        $billingCopy[] = (string)$payment['status'];
    }
}
if (current_lang() !== 'en') {
    localize_texts($billingCopy);
}
$seo_title = localize_text('Plans and Payment History');
$seo_description = localize_text('Manage your Learn.Nectra plan, ad-free access, and payment history.');
$seo_robots = 'noindex, nofollow';
require_once 'includes/header.php';
?>

<section class="page-intro text-white py-12 md:py-16 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background: radial-gradient(circle at 70% 30%, #04AA6D 0%, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <span class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <?php echo h(localize_text('Premium Upgrades')); ?>
        </span>
        <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight"><?php echo h(localize_text('Plans & Payments')); ?></h1>
        <p class="text-slate-300 mt-2 max-w-2xl text-sm sm:text-base"><?php echo h(localize_text('Choose an ad-free premium plan, submit your payment reference, and track your subscriptions all in one place.')); ?></p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?php if($error): ?><div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md"><?php echo h(localize_text($error)); ?></div><?php endif; ?>
    <?php if($success): ?><div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md"><?php echo h(localize_text($success)); ?></div><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <?php foreach($plans as $plan): ?>
                    <article class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-black text-slate-900"><?php echo h(localize_text((string)$plan['name'])); ?></h2>
                                <p class="text-sm text-slate-500 mt-2"><?php echo h(localize_text((string)$plan['description'])); ?></p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-black text-blue-700"><?php echo h($plan['currency']); ?> <?php echo number_format((float)$plan['price'], 2); ?></div>
                                <div class="text-xs font-bold text-slate-400"><?php echo h(localize_text((int)$plan['duration_days'] . ' days')); ?></div>
                            </div>
                        </div>
                        <ul class="mt-5 space-y-2 text-sm text-slate-700">
                            <?php foreach(plan_features($plan['features'] ?? '') as $feature): ?>
                                <li class="flex gap-2"><span class="text-emerald-600 font-black">&check;</span><span><?php echo h(localize_text((string)$feature)); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                        <form method="POST" action="<?php echo h(lang_href('billing')); ?>" class="mt-6 space-y-3">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="request_plan">
                            <input type="hidden" name="plan_id" value="<?php echo (int)$plan['id']; ?>">
                            <input type="text" name="method" placeholder="<?php echo h(t('billing.method')); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" maxlength="80">
                            <input type="text" name="transaction_ref" placeholder="<?php echo h(t('billing.ref')); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" maxlength="160" required>
                            <button type="submit" class="w-full edu-primary-btn px-5 py-3"><?php echo h(t('billing.submit')); ?></button>
                        </form>
                    </article>
                <?php endforeach; ?>
                <?php if(!plans_enabled($pdo)): ?>
                    <div class="col-span-full bg-white rounded-xl border border-gray-200 p-8 text-center">
                        <h2 class="text-2xl font-black text-slate-900"><?php echo h(localize_text('Plans are currently disabled')); ?></h2>
                        <p class="text-slate-500 mt-2"><?php echo h(localize_text('Admin has paused plan purchase requests for now.')); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                <h2 class="text-xl font-black text-slate-900 mb-4"><?php echo h(localize_text('Current Access')); ?></h2>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-4">
                    <p class="font-black <?php echo $active ? 'text-emerald-700' : 'text-slate-900'; ?>"><?php echo $active ? h(localize_text((string)$active['plan_name'])) : h(localize_text('Free Account')); ?></p>
                    <p class="text-sm text-slate-500 mt-1"><?php echo h(localize_text($active ? 'Ad-free premium access is active.' : 'Ads may appear on free accounts.')); ?></p>
                    <?php if($active && !empty($active['ends_at'])): ?><p class="text-sm text-slate-500 mt-1"><?php echo h(localize_text('Valid until ' . date('M j, Y', strtotime($active['ends_at'])))); ?></p><?php endif; ?>
                </div>
                <div class="mt-4 rounded-lg bg-blue-50 border border-blue-100 p-4 text-sm text-blue-900">
                    <?php echo nl2br(h(localize_text((string)platform_setting($pdo, 'payment_instructions', 'Submit your payment reference for admin approval.')))); ?>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                <h2 class="text-xl font-black text-slate-900 mb-4"><?php echo h(localize_text('Payment History')); ?></h2>
                <div class="divide-y divide-slate-100">
                    <?php foreach($payments as $payment): ?>
                        <div class="py-3 text-sm">
                            <div class="flex justify-between gap-3">
                                <span class="font-bold text-slate-800"><?php echo h(localize_text((string)($payment['plan_name'] ?: 'Payment'))); ?></span>
                                <span class="font-black text-slate-900"><?php echo h($payment['currency']); ?> <?php echo number_format((float)$payment['amount'], 2); ?></span>
                            </div>
                            <div class="mt-1 flex justify-between gap-3 text-xs text-slate-500">
                                <span><?php echo h($payment['transaction_ref'] ?: localize_text('No ref')); ?></span>
                                <span class="uppercase font-black"><?php echo h(localize_text((string)$payment['status'])); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(!$payments): ?><p class="text-sm text-slate-500"><?php echo h(localize_text('No payments yet.')); ?></p><?php endif; ?>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
