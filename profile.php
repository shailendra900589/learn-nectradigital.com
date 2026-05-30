<?php
require_once 'includes/db.php';
secure_session_start();

if (empty($_SESSION['student_logged_in'])) {
    redirect(app_path('login'));
}

ensure_student_profile_schema($pdo);

$student_id = (int)$_SESSION['student_id'];
$student = get_student_profile($pdo, $student_id);
if (!$student) {
    session_destroy();
    redirect(app_path('login'));
}
$notificationPrefs = notification_preferences($pdo, $student_id);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = substr(trim($_POST['name'] ?? ''), 0, 120);
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $headline = substr(trim($_POST['headline'] ?? ''), 0, 140);
    $phone = substr(trim($_POST['phone'] ?? ''), 0, 40);
    $location = substr(trim($_POST['location'] ?? ''), 0, 160);
    $website = substr(normalize_profile_url($_POST['website'] ?? ''), 0, 255);
    $linkedin = substr(normalize_profile_url($_POST['linkedin'] ?? ''), 0, 255);
    $github = substr(normalize_profile_url($_POST['github'] ?? ''), 0, 255);
    $skills = substr(trim($_POST['skills'] ?? ''), 0, 1200);
    $bio = substr(trim($_POST['bio'] ?? ''), 0, 2200);
    $newPassword = $_POST['new_password'] ?? '';
    $profilePublic = isset($_POST['profile_public']) ? 1 : 0;
    $publicEmail = isset($_POST['public_email']) ? 1 : 0;
    $publicPhone = isset($_POST['public_phone']) ? 1 : 0;
    $notifInAppEnabled = isset($_POST['notif_inapp_enabled']) ? 1 : 0;
    $notifEmailEnabled = isset($_POST['notif_email_enabled']) ? 1 : 0;
    $notifTypeSystem = isset($_POST['notif_type_system']) ? 1 : 0;
    $notifTypeProgress = isset($_POST['notif_type_progress']) ? 1 : 0;
    $notifTypeRecommendation = isset($_POST['notif_type_recommendation']) ? 1 : 0;
    $notifTypeBilling = isset($_POST['notif_type_billing']) ? 1 : 0;
    $notifTypeSecurity = isset($_POST['notif_type_security']) ? 1 : 0;
    $notifDigestFrequency = $_POST['notif_digest_frequency'] ?? 'off';
    $notifDigestWeekday = (int)($_POST['notif_digest_weekday'] ?? 1);

    if ($name === '' || !$email) {
        $error = 'Name and a valid email are required.';
    }

    if ($error === '') {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = :email AND id != :id LIMIT 1");
        $stmt->execute(['email' => $email, 'id' => $student_id]);
        if ($stmt->fetch()) {
            $error = 'That email address is already used by another account.';
        }
    }

    $profilePhoto = $student['profile_photo'] ?? '';
    if ($error === '' && isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Profile photo upload failed. Please try a different image.';
        } elseif ($_FILES['profile_photo']['size'] > 2 * 1024 * 1024) {
            $error = 'Profile photo must be under 2 MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['profile_photo']['tmp_name']);
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
            ];

            if (!isset($allowed[$mime])) {
                $error = 'Only JPG, PNG, and WebP profile photos are allowed.';
            } else {
                $imageInfo = @getimagesize($_FILES['profile_photo']['tmp_name']);
                if ($imageInfo === false) {
                    $error = 'The uploaded file is not a valid profile image.';
                }
            }

            if ($error === '') {
                $uploadDir = 'assets/uploads/students/';
                $absoluteDir = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $uploadDir);
                if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0755, true)) {
                    $error = 'Could not prepare the profile photo upload folder.';
                } else {
                    $fileName = 'student-' . $student_id . '-' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
                    $target = $absoluteDir . $fileName;
                    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target)) {
                        $profilePhoto = $uploadDir . $fileName;
                    } else {
                        $error = 'Could not save the uploaded profile photo.';
                    }
                }
            }
        }
    }

    if ($error === '' && $newPassword !== '' && strlen($newPassword) < 8) {
        $error = 'New password must be at least 8 characters.';
    }

    if ($error === '') {
        $params = [
            'name' => $name,
            'email' => $email,
            'headline' => $headline,
            'phone' => $phone,
            'location' => $location,
            'website' => $website,
            'linkedin' => $linkedin,
            'github' => $github,
            'skills' => $skills,
            'bio' => $bio,
            'profile_photo' => $profilePhoto,
            'profile_public' => $profilePublic,
            'public_email' => $publicEmail,
            'public_phone' => $publicPhone,
            'id' => $student_id,
        ];

        $passwordSql = '';
        if ($newPassword !== '') {
            $passwordSql = ', password = :password';
            $params['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $stmt = $pdo->prepare("
            UPDATE students
            SET name = :name,
                email = :email,
                headline = :headline,
                phone = :phone,
                location = :location,
                website = :website,
                linkedin = :linkedin,
                github = :github,
                skills = :skills,
                bio = :bio,
                profile_photo = :profile_photo,
                profile_public = :profile_public,
                public_email = :public_email,
                public_phone = :public_phone,
                updated_at = NOW()
                $passwordSql
            WHERE id = :id
        ");
        $stmt->execute($params);
        update_notification_preferences($pdo, $student_id, [
            'notif_inapp_enabled' => $notifInAppEnabled,
            'notif_email_enabled' => $notifEmailEnabled,
            'notif_type_system' => $notifTypeSystem,
            'notif_type_progress' => $notifTypeProgress,
            'notif_type_recommendation' => $notifTypeRecommendation,
            'notif_type_billing' => $notifTypeBilling,
            'notif_type_security' => $notifTypeSecurity,
            'notif_digest_frequency' => $notifDigestFrequency,
            'notif_digest_weekday' => $notifDigestWeekday,
        ]);

        $_SESSION['student_name'] = $name;
        $student = get_student_profile($pdo, $student_id);
        $notificationPrefs = notification_preferences($pdo, $student_id);
        $success = 'Profile updated successfully.';
    }
}

$activity = student_activity_summary($pdo, $student_id);
$subscription = active_subscription($pdo, $student_id);
$paymentsStmt = $pdo->prepare("
    SELECT pay.*, p.name AS plan_name
    FROM payments pay
    LEFT JOIN plans p ON p.id = pay.plan_id
    WHERE pay.student_id = :student_id
    ORDER BY pay.created_at DESC
    LIMIT 6
");
$paymentsStmt->execute(['student_id' => $student_id]);
$recentPayments = $paymentsStmt->fetchAll();
$token = student_public_token($pdo, $student_id, $student['public_token'] ?? null);
$publicUrl = absolute_url('profile/' . $token);
$studentNumber = 'LN-' . str_pad((string)$student_id, 6, '0', STR_PAD_LEFT);
$photo = profile_photo_url($student['profile_photo'] ?? '');

$seo_title = 'Profile and Digital Card';
$seo_description = 'Update your student profile, profile photo, QR card, and public learning resume.';
$seo_canonical = absolute_url('profile');
$seo_robots = 'noindex, nofollow';
require_once 'includes/header.php';
?>

<section class="site-hero-light py-12 border-b border-blue-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div>
                <span class="edu-pill mb-4">Student Profile</span>
                <h1 class="text-4xl md:text-5xl font-black text-slate-950">Profile, QR Card, and Resume</h1>
                <p class="text-slate-600 mt-3 max-w-2xl">Keep your learning identity updated. Your QR card opens a professional public learning profile.</p>
            </div>
            <a href="<?php echo h($publicUrl); ?>" target="_blank" class="bg-white text-blue-700 border border-blue-100 hover:bg-blue-50 font-bold py-3 px-5 rounded-lg shadow-sm">Open Public Profile</a>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?php if($error): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md"><?php echo h($error); ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md"><?php echo h($success); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 learning-surface p-6 md:p-8">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-2xl font-black text-slate-900">Update Profile</h2>
                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Editable</span>
            </div>

            <form method="POST" action="<?php echo h(app_path('profile')); ?>" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Full Name</label>
                        <input type="text" name="name" value="<?php echo h($student['name']); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="<?php echo h($student['email']); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Major / Title</label>
                        <input type="text" name="headline" value="<?php echo h($student['headline'] ?? ''); ?>" placeholder="Digital Marketing, PHP Developer..." class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="<?php echo h($student['phone'] ?? ''); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Location</label>
                        <input type="text" name="location" value="<?php echo h($student['location'] ?? ''); ?>" placeholder="City, Country" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Profile Photo</label>
                        <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/webp" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-3 file:py-2 file:text-sm file:font-bold file:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Website</label>
                        <input type="text" name="website" value="<?php echo h($student['website'] ?? ''); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">LinkedIn</label>
                        <input type="text" name="linkedin" value="<?php echo h($student['linkedin'] ?? ''); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">GitHub</label>
                        <input type="text" name="github" value="<?php echo h($student['github'] ?? ''); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">New Password</label>
                        <input type="password" name="new_password" placeholder="Leave blank to keep current password" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Skills</label>
                    <textarea name="skills" rows="3" placeholder="SEO, PHP, JavaScript, Content Writing" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none"><?php echo h($student['skills'] ?? ''); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Professional Summary</label>
                    <textarea name="bio" rows="5" placeholder="Write a short resume-style summary..." class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none"><?php echo h($student['bio'] ?? ''); ?></textarea>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <h3 class="text-lg font-black text-slate-900 mb-3">Public Profile Privacy</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700">
                            <input type="checkbox" name="profile_public" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($student['profile_public'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            Public profile enabled
                        </label>
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700">
                            <input type="checkbox" name="public_email" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($student['public_email'] ?? 0) === 1 ? 'checked' : ''; ?>>
                            Show email publicly
                        </label>
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700">
                            <input type="checkbox" name="public_phone" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($student['public_phone'] ?? 0) === 1 ? 'checked' : ''; ?>>
                            Show phone publicly
                        </label>
                    </div>
                </div>

                <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-5">
                    <h3 class="text-lg font-black text-slate-900 mb-3">Notification Preferences</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700">
                            <input type="checkbox" name="notif_inapp_enabled" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($notificationPrefs['notif_inapp_enabled'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            Enable in-app notifications
                        </label>
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700">
                            <input type="checkbox" name="notif_email_enabled" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($notificationPrefs['notif_email_enabled'] ?? 0) === 1 ? 'checked' : ''; ?>>
                            Enable email notifications
                        </label>
                    </div>
                    <p class="text-xs uppercase tracking-widest font-black text-slate-400 mb-3">Notification types</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700">
                            <input type="checkbox" name="notif_type_system" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($notificationPrefs['notif_type_system'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            System updates
                        </label>
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700">
                            <input type="checkbox" name="notif_type_progress" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($notificationPrefs['notif_type_progress'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            Progress updates
                        </label>
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700">
                            <input type="checkbox" name="notif_type_recommendation" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($notificationPrefs['notif_type_recommendation'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            Course recommendations
                        </label>
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700">
                            <input type="checkbox" name="notif_type_billing" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($notificationPrefs['notif_type_billing'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            Billing notices
                        </label>
                        <label class="flex items-center gap-3 rounded-lg bg-white border border-slate-200 p-3 font-bold text-sm text-slate-700 md:col-span-2">
                            <input type="checkbox" name="notif_type_security" class="w-5 h-5 text-blue-600 rounded" <?php echo (int)($notificationPrefs['notif_type_security'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            Security alerts
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                        <label class="text-sm font-bold text-slate-700">
                            Digest frequency
                            <select name="notif_digest_frequency" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                <option value="off" <?php echo ($notificationPrefs['notif_digest_frequency'] ?? 'off') === 'off' ? 'selected' : ''; ?>>Off</option>
                                <option value="daily" <?php echo ($notificationPrefs['notif_digest_frequency'] ?? 'off') === 'daily' ? 'selected' : ''; ?>>Daily digest</option>
                                <option value="weekly" <?php echo ($notificationPrefs['notif_digest_frequency'] ?? 'off') === 'weekly' ? 'selected' : ''; ?>>Weekly digest</option>
                            </select>
                        </label>
                        <label class="text-sm font-bold text-slate-700">
                            Weekly digest day
                            <select name="notif_digest_weekday" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                <?php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                $selectedDay = (int)($notificationPrefs['notif_digest_weekday'] ?? 1);
                                foreach ($days as $dayIndex => $dayName):
                                ?>
                                    <option value="<?php echo $dayIndex; ?>" <?php echo $selectedDay === $dayIndex ? 'selected' : ''; ?>><?php echo h($dayName); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                </div>

                <button type="submit" class="edu-primary-btn px-7 py-3 transition">Save Profile</button>
            </form>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-5">
                <h2 class="text-xl font-black text-slate-900 mb-4">Plan & Payment</h2>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 mb-4">
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Current Status</p>
                    <p class="text-lg font-black <?php echo $subscription ? 'text-emerald-700' : 'text-slate-900'; ?> mt-1">
                        <?php echo $subscription ? h($subscription['plan_name'] . ' Active') : 'Free Account'; ?>
                    </p>
                    <?php if($subscription && !empty($subscription['ends_at'])): ?>
                        <p class="text-sm text-slate-500 mt-1">Valid until <?php echo h(date('M j, Y', strtotime($subscription['ends_at']))); ?></p>
                    <?php endif; ?>
                </div>
                <a href="billing" class="block text-center edu-primary-btn px-4 py-3 text-sm">View Plans & Payment History</a>
                <?php if($recentPayments): ?>
                    <div class="mt-4 divide-y divide-slate-100">
                        <?php foreach($recentPayments as $payment): ?>
                            <div class="py-3 text-sm flex justify-between gap-3">
                                <div>
                                    <div class="font-bold text-slate-800"><?php echo h($payment['plan_name'] ?: 'Plan payment'); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo h(date('M j, Y', strtotime($payment['created_at']))); ?></div>
                                </div>
                                <span class="font-black text-slate-700"><?php echo h($payment['currency']); ?> <?php echo number_format((float)$payment['amount'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-black text-slate-900">Digital Card</h2>
                    <button id="downloadCardBtn" class="edu-primary-btn px-4 py-2 text-sm">Download</button>
                </div>

                <div id="student-card" class="student-id-card mx-auto rounded-xl overflow-hidden shadow-2xl bg-blue-600 text-white">
                    <div class="bg-blue-500 px-5 py-3 flex items-center justify-between">
                        <div class="font-black text-2xl">Learn.Nectra</div>
                        <div class="w-11 h-11 rounded-full bg-white/95 text-blue-600 flex items-center justify-center font-black">LN</div>
                    </div>
                    <div class="bg-blue-700 px-5 py-4 grid grid-cols-[1fr_118px] gap-4 min-h-[178px]">
                        <div>
                            <h3 class="text-xl font-black leading-tight"><?php echo h($student['name']); ?></h3>
                            <p class="text-xs mt-4 text-blue-100">Major</p>
                            <p class="font-black"><?php echo h($student['headline'] ?: 'Student Learner'); ?></p>
                            <p class="text-xs mt-4 text-blue-100">ID Number</p>
                            <p class="font-black"><?php echo h($studentNumber); ?></p>
                            <p class="text-xs mt-4 text-blue-100">Email</p>
                            <p class="font-bold text-sm break-all"><?php echo h($student['email']); ?></p>
                        </div>
                        <div class="bg-white text-slate-900 p-2 flex flex-col items-center justify-between">
                            <?php if($photo): ?>
                                <img src="<?php echo h($photo); ?>" alt="Profile photo" class="w-full h-28 object-cover">
                            <?php else: ?>
                                <div class="w-full h-28 bg-slate-100 flex items-center justify-center text-4xl font-black text-blue-600"><?php echo h(strtoupper(substr($student['name'], 0, 1))); ?></div>
                            <?php endif; ?>
                            <div class="text-center">
                                <div class="font-black text-sm">STUDENT</div>
                                <div class="font-serif italic text-lg leading-none"><?php echo h(explode(' ', trim($student['name']))[0]); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-5 bg-blue-500 grid grid-cols-[34px_1fr] gap-3 items-center">
                        <div class="text-white/90 text-xs font-bold [writing-mode:vertical-rl] rotate-180">
                            <?php echo date('M d, Y'); ?>
                        </div>
                        <div class="bg-white rounded-md p-3 flex items-center justify-center">
                            <div id="cardQr" class="student-card-qr"></div>
                        </div>
                    </div>
                    <div class="bg-blue-600 px-5 py-3 grid grid-cols-3 gap-2 text-center">
                        <div>
                            <div class="text-xl font-black"><?php echo (int)$activity['completed_lessons']; ?></div>
                            <div class="text-[10px] uppercase tracking-wider text-blue-100 font-bold">Lessons</div>
                        </div>
                        <div>
                            <div class="text-xl font-black"><?php echo (int)$activity['started_courses']; ?></div>
                            <div class="text-[10px] uppercase tracking-wider text-blue-100 font-bold">Courses</div>
                        </div>
                        <div>
                            <div class="text-xl font-black"><?php echo (int)$activity['completed_courses']; ?></div>
                            <div class="text-[10px] uppercase tracking-wider text-blue-100 font-bold">Done</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 rounded-lg bg-slate-50 border border-slate-200 p-3">
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">QR opens</p>
                    <a href="<?php echo h($publicUrl); ?>" target="_blank" class="text-sm text-blue-600 font-bold break-all"><?php echo h($publicUrl); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const qrTarget = document.getElementById('cardQr');
        if (qrTarget && window.QRCode) {
            new QRCode(qrTarget, {
                text: <?php echo json_encode($publicUrl); ?>,
                width: 205,
                height: 205,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        document.getElementById('downloadCardBtn')?.addEventListener('click', async () => {
            const card = document.getElementById('student-card');
            if (!card || !window.html2canvas) return;
            const canvas = await html2canvas(card, { scale: 3, backgroundColor: null, useCORS: true });
            const link = document.createElement('a');
            link.download = 'learn-nectra-student-card-<?php echo h(slugify($student['name'], 'student')); ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>
