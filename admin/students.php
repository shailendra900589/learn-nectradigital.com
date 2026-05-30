<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();
require_admin_role($pdo, ['owner']);

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
        $description = substr(trim($_POST['description'] ?? ''), 0, 1200);
        $features = substr(trim($_POST['features'] ?? ''), 0, 1600);

        if ($name === '' || $slug === '') {
            $error = 'Plan name and slug are required.';
        } else {
            try {
                if ($planId > 0) {
                    $stmt = $pdo->prepare("
                        UPDATE plans
                        SET name = :name, slug = :slug, price = :price, currency = :currency, duration_days = :duration_days,
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
                        INSERT INTO plans (name, slug, price, currency, duration_days, description, features, ad_free, priority_support, downloadable_resources, certificate_access, is_active)
                        VALUES (:name, :slug, :price, :currency, :duration_days, :description, :features, :ad_free, :priority_support, :downloadable_resources, :certificate_access, :is_active)
                    ");
                    $stmt->execute([
                        'name' => $name,
                        'slug' => $slug,
                        'price' => $price,
                        'currency' => $currency,
                        'duration_days' => $duration,
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
                $endsAt = date('Y-m-d H:i:s', time() + ((int)$plan['duration_days'] * 86400));
                $pdo->prepare("UPDATE student_subscriptions SET status = 'cancelled', updated_at = NOW() WHERE student_id = :student_id AND status = 'active'")
                    ->execute(['student_id' => $studentId]);
                $stmtSub = $pdo->prepare("
                    INSERT INTO student_subscriptions (student_id, plan_id, status, starts_at, ends_at, admin_note, updated_at)
                    VALUES (:student_id, :plan_id, 'active', NOW(), :ends_at, :admin_note, NOW())
                ");
                $stmtSub->execute([
                    'student_id' => $studentId,
                    'plan_id' => $planId,
                    'ends_at' => $endsAt,
                    'admin_note' => substr(trim($_POST['admin_note'] ?? ''), 0, 1000),
                ]);
            }
        }
        $success = 'Student access updated.';
    }

    if ($action === 'payment_status') {
        $paymentId = post_int('payment_id');
        $status = in_array(($_POST['status'] ?? ''), ['approved', 'rejected', 'refunded'], true) ? $_POST['status'] : 'approved';
        $note = substr(trim($_POST['admin_note'] ?? ''), 0, 1000);
        $stmtPayment = $pdo->prepare("SELECT * FROM payments WHERE id = :id LIMIT 1");
        $stmtPayment->execute(['id' => $paymentId]);
        $payment = $stmtPayment->fetch();
        if ($payment) {
            $pdo->prepare("UPDATE payments SET status = :status, admin_note = :note WHERE id = :id")->execute(['status' => $status, 'note' => $note, 'id' => $paymentId]);
            if ($status === 'approved' && (int)$payment['plan_id'] > 0) {
                $stmtPlan = $pdo->prepare("SELECT * FROM plans WHERE id = :id LIMIT 1");
                $stmtPlan->execute(['id' => $payment['plan_id']]);
                $plan = $stmtPlan->fetch();
                if ($plan && plans_enabled($pdo)) {
                    $endsAt = date('Y-m-d H:i:s', time() + ((int)$plan['duration_days'] * 86400));
                    $pdo->prepare("UPDATE student_subscriptions SET status = 'cancelled', updated_at = NOW() WHERE student_id = :student_id AND status = 'active'")
                        ->execute(['student_id' => $payment['student_id']]);
                    $stmtSub = $pdo->prepare("
                        INSERT INTO student_subscriptions (student_id, plan_id, status, starts_at, ends_at, admin_note, updated_at)
                        VALUES (:student_id, :plan_id, 'active', NOW(), :ends_at, :admin_note, NOW())
                    ");
                    $stmtSub->execute([
                        'student_id' => $payment['student_id'],
                        'plan_id' => $payment['plan_id'],
                        'ends_at' => $endsAt,
                        'admin_note' => 'Activated from payment #' . $paymentId,
                    ]);
                }
            }
            $success = 'Payment updated.';
        }
    }

    if ($action === 'save_admin_user') {
        $userId = post_int('user_id');
        $username = trim($_POST['username'] ?? '');
        $displayName = substr(trim($_POST['display_name'] ?? $username), 0, 120);
        $role = in_array(($_POST['role'] ?? 'editor'), ['owner', 'editor', 'reviewer'], true) ? $_POST['role'] : 'editor';
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $password = $_POST['password'] ?? '';

        if (!preg_match('/^[a-zA-Z0-9_.-]{3,40}$/', $username)) {
            $error = 'Admin username must be 3-40 safe characters.';
        } else {
            try {
                if ($userId > 0) {
                    if ($userId === (int)($_SESSION['admin_id'] ?? 0) && $isActive === 0) {
                        $error = 'You cannot deactivate your own admin account.';
                    }
                    $ownerCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'owner' AND is_active = 1")->fetchColumn();
                    $stmtCurrentRole = $pdo->prepare("SELECT role FROM users WHERE id = :id LIMIT 1");
                    $stmtCurrentRole->execute(['id' => $userId]);
                    $currentRole = (string)($stmtCurrentRole->fetchColumn() ?: 'editor');
                    if ($error === '' && $currentRole === 'owner' && $role !== 'owner' && $ownerCount <= 1) {
                        $error = 'At least one active owner is required.';
                    }
                    if ($error === '' && $currentRole === 'owner' && $isActive === 0 && $ownerCount <= 1) {
                        $error = 'At least one active owner is required.';
                    }

                    $params = [
                        'id' => $userId,
                        'username' => $username,
                        'display_name' => $displayName ?: $username,
                        'role' => $role,
                        'is_active' => $isActive,
                    ];
                    $passwordSql = '';
                    if ($password !== '') {
                        if (strlen($password) < 10) {
                            $error = 'New admin password must be at least 10 characters.';
                        } else {
                            $passwordSql = ', password = :password';
                            $params['password'] = password_hash($password, PASSWORD_DEFAULT);
                        }
                    }
                    if ($error === '') {
                        $stmt = $pdo->prepare("
                            UPDATE users
                            SET username = :username,
                                display_name = :display_name,
                                role = :role,
                                is_active = :is_active
                                {$passwordSql}
                            WHERE id = :id
                        ");
                        $stmt->execute($params);
                        $success = 'Admin user updated.';
                    }
                } else {
                    if (strlen($password) < 10) {
                        $error = 'Admin password must be at least 10 characters.';
                    } else {
                        $stmt = $pdo->prepare("
                            INSERT INTO users (username, display_name, password, role, is_active)
                            VALUES (:username, :display_name, :password, :role, :is_active)
                        ");
                        $stmt->execute([
                            'username' => $username,
                            'display_name' => $displayName ?: $username,
                            'password' => password_hash($password, PASSWORD_DEFAULT),
                            'role' => $role,
                            'is_active' => $isActive,
                        ]);
                        $success = 'Admin user created.';
                    }
                }
            } catch (PDOException $e) {
                $error = 'Admin user could not be saved. Username may already exist.';
            }
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
    ORDER BY FIELD(pay.status, 'paid', 'requested', 'approved', 'rejected', 'refunded'), pay.created_at DESC
    LIMIT 80
")->fetchAll();
$planForm = $editPlan ?: ['id' => 0, 'name' => '', 'slug' => '', 'price' => '0.00', 'currency' => 'INR', 'duration_days' => 30, 'description' => '', 'features' => '', 'ad_free' => 1, 'priority_support' => 0, 'downloadable_resources' => 0, 'certificate_access' => 0, 'is_active' => 1];
$adminUsers = $pdo->query("SELECT id, username, display_name, role, is_active FROM users ORDER BY FIELD(role,'owner','editor','reviewer'), id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students & Plans | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo admin_styles(); ?>
</head>
<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden text-slate-800">
    <?php echo admin_sidebar($pdo, 'students'); ?>

    <main class="flex-1 overflow-y-auto">
        <header class="admin-header flex items-center justify-between px-8 sticky top-0 z-10">
            <div>
                <div class="text-sm font-black text-blue-600 uppercase tracking-widest">Users</div>
                <h2 class="text-2xl font-black text-slate-950">Students, Plans & Payments</h2>
            </div>
            <span class="text-sm text-gray-500"><?php echo count($students); ?> students</span>
        </header>

        <div class="p-8 space-y-8">
            <?php if($error): ?><div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md"><?php echo h($error); ?></div><?php endif; ?>
            <?php if($success): ?><div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md"><?php echo h($success); ?></div><?php endif; ?>

            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <form method="POST" action="students" class="bg-white rounded-xl border border-gray-200 p-6">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="settings">
                    <h3 class="text-lg font-black text-slate-900 mb-4">Plan Controls</h3>
                    <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 font-bold text-sm">
                        <input type="checkbox" name="plans_enabled" class="w-5 h-5 text-blue-600 rounded" <?php echo plans_enabled($pdo) ? 'checked' : ''; ?>>
                        Enable plan purchase and admin activation
                    </label>
                    <label class="block text-sm font-bold text-slate-700 mt-4 mb-1">Payment Instructions</label>
                    <textarea name="payment_instructions" rows="5" class="w-full border border-gray-300 rounded-lg p-3 text-sm"><?php echo h(platform_setting($pdo, 'payment_instructions')); ?></textarea>
                    <button class="mt-4 bg-slate-900 hover:bg-blue-600 text-white font-bold px-5 py-2.5 rounded-lg">Save Settings</button>
                </form>

                <form method="POST" action="students" class="bg-white rounded-xl border border-gray-200 p-6 xl:col-span-2">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="save_plan">
                    <input type="hidden" name="plan_id" value="<?php echo (int)$planForm['id']; ?>">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-black text-slate-900"><?php echo $editPlan ? 'Edit Plan' : 'Create Plan'; ?></h3>
                        <?php if($editPlan): ?><a href="students" class="text-sm text-blue-600 font-bold">New Plan</a><?php endif; ?>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input name="name" value="<?php echo h($planForm['name']); ?>" placeholder="Plan name" class="border border-gray-300 rounded-lg p-3" required>
                        <input name="slug" value="<?php echo h($planForm['slug']); ?>" placeholder="plan-slug" class="border border-gray-300 rounded-lg p-3">
                        <input name="price" value="<?php echo h((string)$planForm['price']); ?>" type="number" step="0.01" min="0" class="border border-gray-300 rounded-lg p-3" required>
                        <input name="currency" value="<?php echo h($planForm['currency']); ?>" class="border border-gray-300 rounded-lg p-3" maxlength="8">
                        <input name="duration_days" value="<?php echo (int)$planForm['duration_days']; ?>" type="number" min="1" class="border border-gray-300 rounded-lg p-3">
                        <textarea name="description" rows="2" placeholder="Description" class="md:col-span-3 border border-gray-300 rounded-lg p-3"><?php echo h($planForm['description']); ?></textarea>
                        <textarea name="features" rows="4" placeholder="One feature per line" class="md:col-span-4 border border-gray-300 rounded-lg p-3"><?php echo h($planForm['features']); ?></textarea>
                    </div>
                    <div class="flex flex-wrap gap-3 mt-4 text-sm font-bold">
                        <?php foreach(['ad_free' => 'Ad-free', 'priority_support' => 'Priority support', 'downloadable_resources' => 'Downloads', 'certificate_access' => 'Certificates', 'is_active' => 'Plan active'] as $field => $label): ?>
                            <label class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                                <input type="checkbox" name="<?php echo h($field); ?>" <?php echo (int)$planForm[$field] === 1 ? 'checked' : ''; ?>>
                                <?php echo h($label); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button class="mt-5 bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-lg">Save Plan</button>
                </form>
            </section>

            <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-gray-200">
                    <h3 class="font-black text-slate-900">Admin Role Management</h3>
                </div>
                <div class="p-6 border-b border-slate-100">
                    <form method="POST" action="students" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="save_admin_user">
                        <input type="hidden" name="user_id" value="0">
                        <input name="username" placeholder="username" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                        <input name="display_name" placeholder="display name" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <input name="password" type="password" placeholder="password (10+ chars)" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                        <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="editor">Editor</option>
                            <option value="reviewer">Reviewer</option>
                            <option value="owner">Owner</option>
                        </select>
                        <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" checked> Active</label>
                        <button class="bg-slate-900 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-lg text-sm">Create Admin</button>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-slate-400 border-b border-gray-100"><tr><th class="p-4">Username</th><th class="p-4">Role</th><th class="p-4">Status</th><th class="p-4">Update</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($adminUsers as $adminUser): ?>
                                <tr>
                                    <form method="POST" action="students">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="save_admin_user">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$adminUser['id']; ?>">
                                        <td class="p-4">
                                            <input name="username" value="<?php echo h($adminUser['username']); ?>" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full">
                                            <input name="display_name" value="<?php echo h($adminUser['display_name'] ?? ''); ?>" class="mt-2 border border-gray-300 rounded-lg px-3 py-2 text-sm w-full" placeholder="Display name">
                                        </td>
                                        <td class="p-4">
                                            <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                                <?php foreach(['owner','editor','reviewer'] as $role): ?>
                                                    <option value="<?php echo h($role); ?>" <?php echo ($adminUser['role'] ?? 'editor') === $role ? 'selected' : ''; ?>><?php echo h(ucfirst($role)); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="p-4">
                                            <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" <?php echo (int)($adminUser['is_active'] ?? 1) === 1 ? 'checked' : ''; ?>> Active</label>
                                            <input name="password" type="password" class="mt-2 border border-gray-300 rounded-lg px-3 py-2 text-xs w-full" placeholder="New password (optional)">
                                        </td>
                                        <td class="p-4">
                                            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg text-xs">Save Admin</button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
                                    <td class="p-4 font-bold"><?php echo h($plan['currency']); ?> <?php echo number_format((float)$plan['price'], 2); ?></td>
                                    <td class="p-4 text-slate-600"><?php echo h(implode(', ', array_slice(plan_features($plan['features']), 0, 3))); ?></td>
                                    <td class="p-4 text-right"><a href="students?edit_plan=<?php echo (int)$plan['id']; ?>" class="text-blue-600 font-bold">Edit</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-gray-200">
                    <h3 class="font-black text-slate-900">Students Access Control</h3>
                </div>
                <div class="overflow-x-auto max-h-[720px]">
                    <table class="w-full text-left text-sm">
                        <thead class="sticky top-0 bg-white text-xs uppercase text-slate-400 border-b border-gray-100"><tr><th class="p-4">Student</th><th class="p-4">Current Plan</th><th class="p-4">Controls</th><th class="p-4">Apply Plan</th><th class="p-4 text-right">Save</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($students as $student): ?>
                                <tr>
                                    <form method="POST" action="students">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="update_student">
                                        <input type="hidden" name="student_id" value="<?php echo (int)$student['id']; ?>">
                                        <td class="p-4 align-top">
                                            <div class="font-black text-slate-900"><?php echo h($student['name']); ?></div>
                                            <div class="text-xs text-slate-500"><?php echo h($student['email']); ?></div>
                                            <a href="../profile/<?php echo h($student['public_token'] ?? ''); ?>" target="_blank" class="text-xs text-blue-600 font-bold">Public profile</a>
                                        </td>
                                        <td class="p-4 align-top">
                                            <div class="font-bold"><?php echo h($student['plan_name'] ?: 'Free'); ?></div>
                                            <div class="text-xs text-slate-500"><?php echo $student['ends_at'] ? h(date('M j, Y', strtotime($student['ends_at']))) : 'No expiry'; ?></div>
                                        </td>
                                        <td class="p-4 align-top">
                                            <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                                                <?php foreach(['content_access' => 'Content', 'profile_public' => 'Profile', 'public_email' => 'Email public', 'public_phone' => 'Phone public', 'hide_ads_override' => 'No ads'] as $field => $label): ?>
                                                    <label class="flex items-center gap-2"><input type="checkbox" name="<?php echo h($field); ?>" <?php echo (int)($student[$field] ?? 0) === 1 ? 'checked' : ''; ?>> <?php echo h($label); ?></label>
                                                <?php endforeach; ?>
                                            </div>
                                            <select name="account_status" class="mt-3 border border-gray-300 rounded-lg px-2 py-1 text-xs">
                                                <?php foreach(['active', 'paused', 'blocked'] as $status): ?><option value="<?php echo h($status); ?>" <?php echo ($student['account_status'] ?? 'active') === $status ? 'selected' : ''; ?>><?php echo h(ucfirst($status)); ?></option><?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="p-4 align-top">
                                            <select name="plan_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full" <?php echo plans_enabled($pdo) ? '' : 'disabled'; ?>>
                                                <option value="0">Choose plan</option>
                                                <?php foreach($plans as $plan): if(!$plan['is_active']) continue; ?>
                                                    <option value="<?php echo (int)$plan['id']; ?>"><?php echo h($plan['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label class="mt-2 flex items-center gap-2 text-xs font-bold"><input type="checkbox" name="activate_plan" <?php echo plans_enabled($pdo) ? '' : 'disabled'; ?>> Activate selected plan</label>
                                            <input name="admin_note" placeholder="Admin note" class="mt-2 border border-gray-300 rounded-lg px-3 py-2 text-xs w-full">
                                        </td>
                                        <td class="p-4 align-top text-right"><button class="bg-slate-900 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-lg">Save</button></td>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-gray-200">
                    <h3 class="font-black text-slate-900">Payment History & Approvals</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-slate-400 border-b border-gray-100"><tr><th class="p-4">Student</th><th class="p-4">Plan</th><th class="p-4">Payment</th><th class="p-4">Status</th><th class="p-4 text-right">Action</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($payments as $payment): ?>
                                <tr>
                                    <form method="POST" action="students">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="payment_status">
                                        <input type="hidden" name="payment_id" value="<?php echo (int)$payment['id']; ?>">
                                        <td class="p-4"><div class="font-bold"><?php echo h($payment['student_name'] ?: 'Deleted user'); ?></div><div class="text-xs text-slate-500"><?php echo h($payment['student_email'] ?? ''); ?></div></td>
                                        <td class="p-4"><?php echo h($payment['plan_name'] ?: 'Plan'); ?></td>
                                        <td class="p-4"><div class="font-black"><?php echo h($payment['currency']); ?> <?php echo number_format((float)$payment['amount'], 2); ?></div><div class="text-xs text-slate-500"><?php echo h($payment['transaction_ref'] ?: 'No reference'); ?></div></td>
                                        <td class="p-4"><span class="uppercase text-xs font-black"><?php echo h($payment['status']); ?></span></td>
                                        <td class="p-4 text-right">
                                            <select name="status" class="border border-gray-300 rounded-lg px-2 py-1 text-xs">
                                                <?php foreach(['approved', 'rejected', 'refunded'] as $status): ?><option value="<?php echo h($status); ?>"><?php echo h(ucfirst($status)); ?></option><?php endforeach; ?>
                                            </select>
                                            <input name="admin_note" placeholder="Note" class="border border-gray-300 rounded-lg px-2 py-1 text-xs">
                                            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs">Update</button>
                                        </td>
                                    </form>
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
