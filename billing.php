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

$seo_title = 'Plans and Payment History';
$seo_description = 'Manage your Learn.Nectra plan, ad-free access, and payment history.';
$seo_robots = 'noindex, nofollow';
require_once 'includes/header.php';
?>

<section class="site-hero-light py-12 border-b border-blue-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="edu-pill mb-4">Premium Access</span>
        <h1 class="text-4xl md:text-5xl font-black text-slate-950">Plans & Payments</h1>
        <p class="text-slate-600 mt-3 max-w-2xl">Choose a premium plan, submit your payment reference, and track every payment from one place.</p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?php if($error): ?><div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md"><?php echo h($error); ?></div><?php endif; ?>
    <?php if($success): ?><div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md"><?php echo h($success); ?></div><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <?php foreach($plans as $plan): ?>
                    <article class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-black text-slate-900"><?php echo h($plan['name']); ?></h2>
                                <p class="text-sm text-slate-500 mt-2"><?php echo h($plan['description']); ?></p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-black text-blue-700"><?php echo h($plan['currency']); ?> <?php echo number_format((float)$plan['price'], 2); ?></div>
                                <div class="text-xs font-bold text-slate-400"><?php echo (int)$plan['duration_days']; ?> days</div>
                            </div>
                        </div>
                        <ul class="mt-5 space-y-2 text-sm text-slate-700">
                            <?php foreach(plan_features($plan['features'] ?? '') as $feature): ?>
                                <li class="flex gap-2"><span class="text-emerald-600 font-black">&check;</span><span><?php echo h($feature); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                        <form method="POST" action="billing" class="mt-6 space-y-3">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="request_plan">
                            <input type="hidden" name="plan_id" value="<?php echo (int)$plan['id']; ?>">
                            <input type="text" name="method" placeholder="Payment method" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" maxlength="80">
                            <input type="text" name="transaction_ref" placeholder="Transaction ID / UTR / reference" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" maxlength="160" required>
                            <button type="submit" class="w-full edu-primary-btn px-5 py-3">Submit Payment</button>
                        </form>
                    </article>
                <?php endforeach; ?>
                <?php if(!plans_enabled($pdo)): ?>
                    <div class="col-span-full bg-white rounded-xl border border-gray-200 p-8 text-center">
                        <h2 class="text-2xl font-black text-slate-900">Plans are currently disabled</h2>
                        <p class="text-slate-500 mt-2">Admin has paused plan purchase requests for now.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                <h2 class="text-xl font-black text-slate-900 mb-4">Current Access</h2>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-4">
                    <p class="font-black <?php echo $active ? 'text-emerald-700' : 'text-slate-900'; ?>"><?php echo $active ? h($active['plan_name']) : 'Free Account'; ?></p>
                    <p class="text-sm text-slate-500 mt-1"><?php echo $active ? 'Ad-free premium access is active.' : 'Ads may appear on free accounts.'; ?></p>
                    <?php if($active && !empty($active['ends_at'])): ?><p class="text-sm text-slate-500 mt-1">Valid until <?php echo h(date('M j, Y', strtotime($active['ends_at']))); ?></p><?php endif; ?>
                </div>
                <div class="mt-4 rounded-lg bg-blue-50 border border-blue-100 p-4 text-sm text-blue-900">
                    <?php echo nl2br(h(platform_setting($pdo, 'payment_instructions', 'Submit your payment reference for admin approval.'))); ?>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                <h2 class="text-xl font-black text-slate-900 mb-4">Payment History</h2>
                <div class="divide-y divide-slate-100">
                    <?php foreach($payments as $payment): ?>
                        <div class="py-3 text-sm">
                            <div class="flex justify-between gap-3">
                                <span class="font-bold text-slate-800"><?php echo h($payment['plan_name'] ?: 'Payment'); ?></span>
                                <span class="font-black text-slate-900"><?php echo h($payment['currency']); ?> <?php echo number_format((float)$payment['amount'], 2); ?></span>
                            </div>
                            <div class="mt-1 flex justify-between gap-3 text-xs text-slate-500">
                                <span><?php echo h($payment['transaction_ref'] ?: 'No ref'); ?></span>
                                <span class="uppercase font-black"><?php echo h($payment['status']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(!$payments): ?><p class="text-sm text-slate-500">No payments yet.</p><?php endif; ?>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
