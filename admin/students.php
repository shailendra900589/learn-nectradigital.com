<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'settings') {
        update_platform_setting($pdo, 'plans_enabled', isset($_POST['plans_enabled']) ? '1' : '0');
        update_platform_setting($pdo, 'payment_instructions', substr(trim($_POST['payment_instructions'] ?? ''), 0, 1200));
        $success = 'Platform settings updated.';
    }

    if ($action === 'save_plan') {
        $planId = post_int('plan_id');
        $name = substr(trim($_POST['name'] ?? ''), 0, 120);
        $slug = slugify($_POST['slug'] ?? $name, '');
        $price = max(0, (float)($_POST['price'] ?? 0));
        $currency = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $_POST['currency'] ?? 'INR'), 0, 8)) ?: 'INR';
        $duration = max(1, post_int('duration_days', 30));
        $courseLimitRaw = trim((string)($_POST['course_limit'] ?? ''));
        $courseLimit = $courseLimitRaw === '' ? null : max(1, (int)$courseLimitRaw);
        $offerPriceRaw = trim((string)($_POST['offer_price'] ?? ''));
        $offerPrice = $offerPriceRaw === '' ? null : max(0, (float)$offerPriceRaw);
        $offerLabel = substr(trim((string)($_POST['offer_label'] ?? '')), 0, 80) ?: null;
        $offerEndsRaw = trim((string)($_POST['offer_ends_at'] ?? ''));
        $offerEndsAt = preg_match('/^\d{4}-\d{2}-\d{2}$/', $offerEndsRaw) ? $offerEndsRaw . ' 23:59:59' : null;
        $description = substr(trim($_POST['description'] ?? ''), 0, 1200);
        $features = substr(trim($_POST['features'] ?? ''), 0, 1600);

        if ($name === '' || $slug === '') {
            $error = 'Plan name and slug are required.';
        } else {
            try {
                if ($planId > 0) {
                    $stmt = $pdo->prepare("
                        UPDATE plans
                        SET name = :name, slug = :slug, price = :price, offer_price = :offer_price, offer_label = :offer_label, offer_ends_at = :offer_ends_at,
                            currency = :currency, duration_days = :duration_days, course_limit = :course_limit,
                            description = :description, features = :features, ad_free = :ad_free, priority_support = :priority_support,
                            downloadable_resources = :downloadable_resources, certificate_access = :certificate_access, is_active = :is_active
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        'name' => $name,
                        'slug' => $slug,
                        'price' => $price,
                        'currency' => $currency,
                        'duration_days' => $duration,
                        'course_limit' => $courseLimit,
                        'offer_price' => $offerPrice,
                        'offer_label' => $offerLabel,
                        'offer_ends_at' => $offerEndsAt,
                        'description' => $description,
                        'features' => $features,
                        'ad_free' => isset($_POST['ad_free']) ? 1 : 0,
                        'priority_support' => isset($_POST['priority_support']) ? 1 : 0,
                        'downloadable_resources' => isset($_POST['downloadable_resources']) ? 1 : 0,
                        'certificate_access' => isset($_POST['certificate_access']) ? 1 : 0,
                        'is_active' => isset($_POST['is_active']) ? 1 : 0,
                        'id' => $planId,
                    ]);
                    $success = 'Plan updated.';
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO plans (name, slug, price, offer_price, offer_label, offer_ends_at, currency, duration_days, course_limit, description, features, ad_free, priority_support, downloadable_resources, certificate_access, is_active)
                        VALUES (:name, :slug, :price, :offer_price, :offer_label, :offer_ends_at, :currency, :duration_days, :course_limit, :description, :features, :ad_free, :priority_support, :downloadable_resources, :certificate_access, :is_active)
                    ");
                    $stmt->execute([
                        'name' => $name,
                        'slug' => $slug,
                        'price' => $price,
                        'currency' => $currency,
                        'duration_days' => $duration,
                        'course_limit' => $courseLimit,
                        'offer_price' => $offerPrice,
                        'offer_label' => $offerLabel,
                        'offer_ends_at' => $offerEndsAt,
                        'description' => $description,
                        'features' => $features,
                        'ad_free' => isset($_POST['ad_free']) ? 1 : 0,
                        'priority_support' => isset($_POST['priority_support']) ? 1 : 0,
                        'downloadable_resources' => isset($_POST['downloadable_resources']) ? 1 : 0,
                        'certificate_access' => isset($_POST['certificate_access']) ? 1 : 0,
                        'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    ]);
                    $success = 'Plan created.';
                }
            } catch (PDOException $e) {
                $error = 'Plan could not be saved. Use a unique slug.';
            }
        }
    }

    if ($action === 'update_student') {
        $studentId = post_int('student_id');
        $stmt = $pdo->prepare("
            UPDATE students
            SET content_access = :content_access,
                profile_public = :profile_public,
                public_email = :public_email,
                public_phone = :public_phone,
                hide_ads_override = :hide_ads_override,
                account_status = :account_status
            WHERE id = :id
        ");
        $status = in_array(($_POST['account_status'] ?? 'active'), ['active', 'paused', 'blocked'], true) ? $_POST['account_status'] : 'active';
        $stmt->execute([
            'content_access' => isset($_POST['content_access']) ? 1 : 0,
            'profile_public' => isset($_POST['profile_public']) ? 1 : 0,
            'public_email' => isset($_POST['public_email']) ? 1 : 0,
            'public_phone' => isset($_POST['public_phone']) ? 1 : 0,
            'hide_ads_override' => isset($_POST['hide_ads_override']) ? 1 : 0,
            'account_status' => $status,
            'id' => $studentId,
        ]);

        $planId = post_int('plan_id');
        if (plans_enabled($pdo) && $planId > 0 && isset($_POST['activate_plan'])) {
            $stmtPlan = $pdo->prepare("SELECT * FROM plans WHERE id = :id AND is_active = 1 LIMIT 1");
            $stmtPlan->execute(['id' => $planId]);
            $plan = $stmtPlan->fetch();
            if ($plan) {
                $courseIds = array_map('intval', (array)($_POST['course_ids'] ?? []));
                $grantedId = activate_plan_for_student($pdo, $studentId, $plan, 'Admin: ' . trim((string)($_POST['admin_note'] ?? '')), $courseIds);
                defer_after_response(static fn() => send_package_mail($pdo, $studentId, $grantedId));
            }
        }
        $success = 'Student access updated.';
    }

    if ($action === 'cancel_subscription') {
        $pdo->prepare("UPDATE student_subscriptions SET status = 'cancelled', updated_at = NOW() WHERE id = :id AND status = 'active'")
            ->execute(['id' => post_int('subscription_id')]);
        $success = 'Package cancelled.';
    }

    if ($action === 'payment_status') {
        $paymentId = post_int('payment_id');
        $status = in_array(($_POST['status'] ?? ''), ['approved', 'rejected', 'refunded'], true) ? $_POST['status'] : 'approved';
        $note = substr(trim($_POST['admin_note'] ?? ''), 0, 1000);
        $stmtPayment = $pdo->prepare("SELECT * FROM payments WHERE id = :id LIMIT 1");
        $stmtPayment->execute(['id' => $paymentId]);
        $payment = $stmtPayment->fetch();
        if ($payment) {
            $alreadyApproved = $payment['status'] === 'approved';
            $pdo->prepare("UPDATE payments SET status = :status, admin_note = :note WHERE id = :id")->execute(['status' => $status, 'note' => $note, 'id' => $paymentId]);
            if ($status === 'approved' && !$alreadyApproved && (int)$payment['plan_id'] > 0) {
                $stmtPlan = $pdo->prepare("SELECT * FROM plans WHERE id = :id LIMIT 1");
                $stmtPlan->execute(['id' => $payment['plan_id']]);
                $plan = $stmtPlan->fetch();
                if ($plan) {
                    $subscriptionId = activate_plan_for_student($pdo, (int)$payment['student_id'], $plan, 'Activated from payment #' . $paymentId, $payment['course_id'] ? [(int)$payment['course_id']] : []);
                    $pdo->prepare("UPDATE payments SET subscription_id = :sid WHERE id = :id")->execute(['sid' => $subscriptionId, 'id' => $paymentId]);
                    $buyerId = (int)$payment['student_id'];
                    defer_after_response(static fn() => send_package_mail($pdo, $buyerId, $subscriptionId));
                }
            }
            if (in_array($status, ['rejected', 'refunded'], true) && (int)$payment['subscription_id'] > 0) {
                $pdo->prepare("UPDATE student_subscriptions SET status = 'cancelled', updated_at = NOW() WHERE id = :id")
                    ->execute(['id' => $payment['subscription_id']]);
            }
            $success = 'Payment updated.';
        }
    }
}

$editPlan = null;
if (isset($_GET['edit_plan'])) {
    $stmt = $pdo->prepare("SELECT * FROM plans WHERE id = :id");
    $stmt->execute(['id' => get_int('edit_plan')]);
    $editPlan = $stmt->fetch();
}

$plans = $pdo->query("SELECT * FROM plans ORDER BY is_active DESC, price ASC, id DESC")->fetchAll();
$students = $pdo->query("
    SELECT s.*,
           sub.status AS subscription_status,
           sub.ends_at,
           p.name AS plan_name
    FROM students s
    LEFT JOIN student_subscriptions sub ON sub.id = (
        SELECT ss.id FROM student_subscriptions ss
        WHERE ss.student_id = s.id AND ss.status = 'active' AND (ss.ends_at IS NULL OR ss.ends_at >= NOW())
        ORDER BY ss.ends_at DESC, ss.id DESC LIMIT 1
    )
    LEFT JOIN plans p ON p.id = sub.plan_id
    ORDER BY s.id DESC
")->fetchAll();
$payments = $pdo->query("
    SELECT pay.*, s.name AS student_name, s.email AS student_email, p.name AS plan_name
    FROM payments pay
    LEFT JOIN students s ON s.id = pay.student_id
    LEFT JOIN plans p ON p.id = pay.plan_id
    WHERE NOT (pay.method = 'razorpay' AND pay.status = 'requested')
    ORDER BY FIELD(pay.status, 'paid', 'requested', 'approved', 'rejected', 'refunded'), pay.created_at DESC
    LIMIT 80
")->fetchAll();
$allCourses = $pdo->query("SELECT id, title FROM courses ORDER BY title ASC")->fetchAll();
$studentPackages = [];
foreach ($students as $student) {
    foreach (active_subscriptions($pdo, (int)$student['id']) as $package) {
        $package['courses'] = $package['course_limit'] === null ? [] : subscription_courses($pdo, (int)$package['id']);
        $studentPackages[(int)$student['id']][] = $package;
    }
}
$planForm = $editPlan ?: ['id' => 0, 'name' => '', 'slug' => '', 'price' => '0.00', 'currency' => 'INR', 'duration_days' => 365, 'course_limit' => null, 'description' => '', 'features' => '', 'ad_free' => 1, 'priority_support' => 0, 'downloadable_resources' => 0, 'certificate_access' => 0, 'is_active' => 1];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students & Plans | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo admin_styles(); ?>
</head>
<body class="admin-workspace">
    <?php echo admin_sidebar($pdo, 'students'); ?>

    <main class="admin-main flex-1 overflow-y-auto">
        <header class="admin-header flex items-center justify-between px-8 sticky top-0 z-10">
            <div>
                <div class="text-sm font-black text-blue-600 uppercase tracking-widest">Users</div>
                <h1 class="text-2xl font-black text-slate-950">Students, Plans & Payments</h1>
            </div>
            <nav class="admin-section-nav" aria-label="Student management sections">
                <a href="#plans">Plans</a>
                <a href="#student-access">Students</a>
                <a href="#payment-history">Payments</a>
            </nav>
        </header>

        <div class="admin-content space-y-8">
            <?php if($error): ?><div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md"><?php echo h($error); ?></div><?php endif; ?>
            <?php if($success): ?><div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md"><?php echo h($success); ?></div><?php endif; ?>

            <section class="admin-plan-layout" id="plans">
                <form method="POST" action="students" class="bg-white rounded-xl border border-gray-200 p-6">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="settings">
                    <h3 class="text-lg font-black text-slate-900 mb-4">Plan Controls</h3>
                    <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 font-bold text-sm">
                        <input type="checkbox" name="plans_enabled" class="w-5 h-5 text-blue-600 rounded" <?php echo plans_enabled($pdo) ? 'checked' : ''; ?>>
                        Enable plan purchase and admin activation
                    </label>
                    <label for="payment-instructions" class="block text-sm font-bold text-slate-700 mt-4 mb-1">Payment instructions</label>
                    <textarea id="payment-instructions" name="payment_instructions" rows="5" class="admin-input text-sm"><?php echo h(platform_setting($pdo, 'payment_instructions')); ?></textarea>
                    <button class="mt-4 admin-btn-dark">Save Settings</button>
                </form>

                <form method="POST" action="students" class="bg-white rounded-xl border border-gray-200 p-6">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="save_plan">
                    <input type="hidden" name="plan_id" value="<?php echo (int)$planForm['id']; ?>">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-black text-slate-900"><?php echo $editPlan ? 'Edit Plan' : 'Create Plan'; ?></h3>
                        <?php if($editPlan): ?><a href="students" class="text-sm text-blue-600 font-bold">New Plan</a><?php endif; ?>
                    </div>
                    <div class="admin-plan-fields">
                        <label class="admin-field"><span>Plan name</span>
                            <input name="name" value="<?php echo h($planForm['name']); ?>" placeholder="Plan name" class="admin-input" required>
                        </label>
                        <label class="admin-field"><span>URL slug</span>
                            <input name="slug" value="<?php echo h($planForm['slug']); ?>" placeholder="plan-slug" class="admin-input">
                        </label>
                        <label class="admin-field"><span>Price</span>
                            <input name="price" value="<?php echo h((string)$planForm['price']); ?>" type="number" step="0.01" min="0" class="admin-input" required>
                        </label>
                        <label class="admin-field"><span>Currency</span>
                            <input name="currency" value="<?php echo h($planForm['currency']); ?>" class="admin-input" maxlength="8">
                        </label>
                        <label class="admin-field"><span>Duration in days</span>
                            <input name="duration_days" value="<?php echo (int)$planForm['duration_days']; ?>" type="number" min="1" class="admin-input">
                        </label>
                        <label class="admin-field"><span>Courses included (blank = all courses)</span>
                            <input name="course_limit" value="<?php echo $planForm['course_limit'] === null ? '' : (int)$planForm['course_limit']; ?>" type="number" min="1" placeholder="All" class="admin-input">
                        </label>
                        <label class="admin-field"><span>Offer price (blank = no offer)</span>
                            <input name="offer_price" value="<?php echo ($planForm['offer_price'] ?? null) === null ? '' : h((string)$planForm['offer_price']); ?>" type="number" step="0.01" min="0" placeholder="e.g. 149" class="admin-input">
                        </label>
                        <label class="admin-field"><span>Offer label</span>
                            <input name="offer_label" value="<?php echo h((string)($planForm['offer_label'] ?? '')); ?>" maxlength="80" placeholder="Diwali offer" class="admin-input">
                        </label>
                        <label class="admin-field"><span>Offer ends on (blank = no end date)</span>
                            <input name="offer_ends_at" value="<?php echo !empty($planForm['offer_ends_at']) ? h(date('Y-m-d', strtotime((string)$planForm['offer_ends_at']))) : ''; ?>" type="date" class="admin-input">
                        </label>
                        <label class="admin-field admin-field-full"><span>Description</span>
                            <textarea name="description" rows="2" placeholder="Description" class="admin-input"><?php echo h($planForm['description']); ?></textarea>
                        </label>
                        <label class="admin-field admin-field-full"><span>Features</span>
                            <textarea name="features" rows="4" placeholder="One feature per line" class="admin-input"><?php echo h($planForm['features']); ?></textarea>
                        </label>
                    </div>
                    <div class="flex flex-wrap gap-3 mt-4 text-sm font-bold">
                        <?php foreach(['ad_free' => 'Ad-free', 'priority_support' => 'Priority support', 'downloadable_resources' => 'Downloads', 'certificate_access' => 'Certificates', 'is_active' => 'Plan active'] as $field => $label): ?>
                            <label class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                                <input type="checkbox" name="<?php echo h($field); ?>" <?php echo (int)$planForm[$field] === 1 ? 'checked' : ''; ?>>
                                <?php echo h($label); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button class="mt-5 admin-btn-primary">Save Plan</button>
                </form>
            </section>

            <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-gray-200 flex justify-between">
                    <h3 class="font-black text-slate-900">Existing Plans</h3>
                    <span class="text-xs font-bold text-slate-500"><?php echo count($plans); ?> total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-slate-400 border-b border-gray-100"><tr><th class="p-4">Plan</th><th class="p-4">Price</th><th class="p-4">Features</th><th class="p-4 text-right">Action</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($plans as $plan): ?>
                                <tr>
                                    <td class="p-4"><div class="font-black"><?php echo h($plan['name']); ?></div><div class="text-xs text-slate-500"><?php echo h($plan['slug']); ?> - <?php echo $plan['is_active'] ? 'Active' : 'Off'; ?></div></td>
                                    <td class="p-4 font-bold"><?php echo h($plan['currency']); ?> <?php echo number_format((float)$plan['price'], 2); ?><?php if (plan_offer_active($plan)): ?> <span class="text-rose-600">&rarr; <?php echo number_format(plan_effective_price($plan), 2); ?> offer</span><?php endif; ?><div class="text-xs text-slate-500 font-normal"><?php echo $plan['course_limit'] === null ? 'All courses' : (int)$plan['course_limit'] . ' course(s)'; ?> &middot; <?php echo (int)$plan['duration_days']; ?> days</div></td>
                                    <td class="p-4 text-slate-600"><?php echo h(implode(', ', array_slice(plan_features($plan['features']), 0, 3))); ?></td>
                                    <td class="p-4 text-right"><a href="students?edit_plan=<?php echo (int)$plan['id']; ?>" class="text-blue-600 font-bold">Edit</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="student-access" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-gray-200">
                    <h3 class="font-black text-slate-900">Students Access Control</h3>
                </div>
                <div class="overflow-x-auto max-h-[720px]">
                    <table class="w-full text-left text-sm">
                        <thead class="sticky top-0 bg-white text-xs uppercase text-slate-400 border-b border-gray-100"><tr><th class="p-4">Student</th><th class="p-4">Current Plan</th><th class="p-4">Controls</th><th class="p-4">Apply Plan</th><th class="p-4 text-right">Save</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($students as $student): ?>
                                <tr>
                                        <td class="p-4 align-top">
                                            <div class="font-black text-slate-900"><?php echo h($student['name']); ?></div>
                                            <div class="text-xs text-slate-500"><?php echo h($student['email']); ?></div>
                                            <a href="../profile/<?php echo h($student['public_token'] ?? ''); ?>" target="_blank" class="text-xs text-blue-600 font-bold">Public profile</a>
                                        </td>
                                        <td class="p-4 align-top min-w-[220px]">
                                            <?php if (empty($studentPackages[(int)$student['id']])): ?>
                                                <div class="font-bold">Free</div>
                                            <?php endif; ?>
                                            <?php foreach ($studentPackages[(int)$student['id']] ?? [] as $package): ?>
                                                <div class="mb-3 rounded-lg border border-emerald-200 bg-emerald-50/60 p-2">
                                                    <div class="font-bold"><?php echo h($package['plan_name'] ?: 'Package'); ?></div>
                                                    <div class="text-xs text-slate-500">Until <?php echo $package['ends_at'] ? h(date('M j, Y', strtotime($package['ends_at']))) : 'no expiry'; ?></div>
                                                    <div class="text-xs text-slate-600 mt-1">
                                                        <?php if ($package['course_limit'] === null): ?>All courses<?php else: ?>
                                                            <?php echo (int)$package['courses_used']; ?>/<?php echo (int)$package['course_limit']; ?>:
                                                            <?php echo h(implode(', ', array_column($package['courses'], 'title')) ?: 'none chosen yet'); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <form method="POST" action="students" class="mt-1" onsubmit="return confirm('Cancel this package?');">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="action" value="cancel_subscription">
                                                        <input type="hidden" name="subscription_id" value="<?php echo (int)$package['id']; ?>">
                                                        <button class="text-xs font-bold text-red-600 hover:underline">Cancel package</button>
                                                    </form>
                                                </div>
                                            <?php endforeach; ?>
                                        </td>
                                        <td class="p-4 align-top">
                                            <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                                                <?php foreach(['content_access' => 'Content', 'profile_public' => 'Profile', 'public_email' => 'Email public', 'public_phone' => 'Phone public', 'hide_ads_override' => 'Ad-free (manual)'] as $field => $label): ?>
                                                    <label class="flex items-center gap-2"><input form="student-form-<?php echo (int)$student['id']; ?>" type="checkbox" name="<?php echo h($field); ?>" <?php echo (int)($student[$field] ?? 0) === 1 ? 'checked' : ''; ?>> <?php echo h($label); ?></label>
                                                <?php endforeach; ?>
                                            </div>
                                            <select form="student-form-<?php echo (int)$student['id']; ?>" aria-label="Account status" name="account_status" class="mt-3 border border-gray-300 rounded-lg px-2 py-1 text-xs">
                                                <?php foreach(['active', 'paused', 'blocked'] as $status): ?><option value="<?php echo h($status); ?>" <?php echo ($student['account_status'] ?? 'active') === $status ? 'selected' : ''; ?>><?php echo h(ucfirst($status)); ?></option><?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="p-4 align-top">
                                            <select form="student-form-<?php echo (int)$student['id']; ?>" aria-label="Plan" name="plan_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full" <?php echo plans_enabled($pdo) ? '' : 'disabled'; ?>>
                                                <option value="0">Choose plan</option>
                                                <?php foreach($plans as $plan): if(!$plan['is_active']) continue; ?>
                                                    <option value="<?php echo (int)$plan['id']; ?>"><?php echo h($plan['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <select form="student-form-<?php echo (int)$student['id']; ?>" aria-label="Courses for the package" name="course_ids[]" multiple size="4" class="mt-2 border border-gray-300 rounded-lg px-2 py-1 text-xs w-full">
                                                <?php foreach ($allCourses as $courseOption): ?>
                                                    <option value="<?php echo (int)$courseOption['id']; ?>"><?php echo h($courseOption['title']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <p class="text-[11px] text-slate-500 mt-1">For course packs: Ctrl+click to pick courses. Ignored for All Access.</p>
                                            <label class="mt-2 flex items-center gap-2 text-xs font-bold"><input form="student-form-<?php echo (int)$student['id']; ?>" type="checkbox" name="activate_plan" <?php echo plans_enabled($pdo) ? '' : 'disabled'; ?>> Activate selected plan</label>
                                            <input form="student-form-<?php echo (int)$student['id']; ?>" name="admin_note" placeholder="Admin note" class="mt-2 border border-gray-300 rounded-lg px-3 py-2 text-xs w-full">
                                        </td>
                                        <td class="p-4 align-top text-right">
                                    <form id="student-form-<?php echo (int)$student['id']; ?>" method="POST" action="students">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="update_student">
                                        <input type="hidden" name="student_id" value="<?php echo (int)$student['id']; ?>">
                                        <button class="bg-slate-900 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-lg">Save</button>
                                    </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="payment-history" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-gray-200">
                    <h3 class="font-black text-slate-900">Payment History & Approvals</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-slate-400 border-b border-gray-100"><tr><th class="p-4">Student</th><th class="p-4">Plan</th><th class="p-4">Payment</th><th class="p-4">Status</th><th class="p-4 text-right">Action</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($payments as $payment): ?>
                                <tr>
                                        <td class="p-4"><div class="font-bold"><?php echo h($payment['student_name'] ?: 'Deleted user'); ?></div><div class="text-xs text-slate-500"><?php echo h($payment['student_email'] ?? ''); ?></div></td>
                                        <td class="p-4"><?php echo h($payment['plan_name'] ?: 'Plan'); ?></td>
                                        <td class="p-4"><div class="font-black"><?php echo h($payment['currency']); ?> <?php echo number_format((float)$payment['amount'], 2); ?></div><div class="text-xs text-slate-500"><?php echo h($payment['transaction_ref'] ?: 'No reference'); ?></div></td>
                                        <td class="p-4"><span class="uppercase text-xs font-black"><?php echo h($payment['status']); ?></span></td>
                                        <td class="p-4 text-right">
                                    <form method="POST" action="students">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="payment_status">
                                        <input type="hidden" name="payment_id" value="<?php echo (int)$payment['id']; ?>">
                                            <select aria-label="Payment status" name="status" class="border border-gray-300 rounded-lg px-2 py-1 text-xs">
                                                <?php foreach(['approved', 'rejected', 'refunded'] as $status): ?><option value="<?php echo h($status); ?>"><?php echo h(ucfirst($status)); ?></option><?php endforeach; ?>
                                            </select>
                                            <input name="admin_note" placeholder="Note" class="border border-gray-300 rounded-lg px-2 py-1 text-xs">
                                            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs">Update</button>
                                    </form>
                                        </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(!$payments): ?><tr><td colspan="5" class="p-8 text-center text-slate-500">No payments yet.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
