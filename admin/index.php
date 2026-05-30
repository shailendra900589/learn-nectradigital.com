<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();
require_admin_role($pdo, ['owner', 'editor', 'reviewer']);
$adminRole = current_admin_role($pdo);

if (isset($_GET['export']) && $_GET['export'] !== '') {
    $allowedExports = ['traffic_daily', 'traffic_hourly', 'traffic_distribution', 'traffic_engagement'];
    $exportType = in_array((string)$_GET['export'], $allowedExports, true) ? (string)$_GET['export'] : '';
    if ($exportType !== '') {
        $emitCsv = function (string $filename, array $header, array $rows): void {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $output = fopen('php://output', 'wb');
            if ($output === false) {
                exit;
            }
            fputcsv($output, $header);
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            exit;
        };

        if ($exportType === 'traffic_daily') {
            $daily = visitor_trend_last_days($pdo, 90);
            $rows = array_map(fn($point) => [(string)$point['date'], (int)$point['unique_visitors'], (int)$point['total_hits']], $daily);
            $emitCsv('traffic-daily-last-90-days.csv', ['date', 'unique_visitors', 'total_hits'], $rows);
        }

        if ($exportType === 'traffic_hourly') {
            $hourly = visitor_hourly_trend_today($pdo);
            $rows = array_map(fn($point) => [(string)$point['hour'], (int)$point['unique'], (int)$point['hits']], $hourly);
            $emitCsv('traffic-hourly-today.csv', ['hour', 'unique_visitors', 'total_hits'], $rows);
        }

        if ($exportType === 'traffic_distribution') {
            $distribution = visitor_distribution_summary($pdo);
            $rows = [];
            foreach (($distribution['countries'] ?? []) as $item) {
                $rows[] = ['country', (string)$item['label'], (int)$item['count']];
            }
            foreach (($distribution['devices'] ?? []) as $item) {
                $rows[] = ['device', (string)$item['label'], (int)$item['count']];
            }
            foreach (($distribution['browsers'] ?? []) as $item) {
                $rows[] = ['browser', (string)$item['label'], (int)$item['count']];
            }
            $emitCsv('traffic-distribution.csv', ['dimension', 'label', 'count'], $rows);
        }

        if ($exportType === 'traffic_engagement') {
            $engagement = visitor_engagement_estimates($pdo);
            $rows = [];
            foreach ($engagement as $metric => $value) {
                $rows[] = [$metric, $value];
            }
            $emitCsv('traffic-engagement.csv', ['metric', 'value'], $rows);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    verify_csrf();
    session_destroy();
    header("Location: login");
    exit;
}
$digestFlash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'run_digest_now') {
    verify_csrf();
    $digestResult = run_due_notification_digests($pdo, 300);
    $digestFlash = 'Digest run complete: checked ' . (int)$digestResult['checked'] . ', eligible ' . (int)$digestResult['eligible'] . ', sent ' . (int)$digestResult['sent'] . ', failed ' . (int)$digestResult['failed'] . '.';
}

$stats = [
    'courses' => (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
    'chapters' => (int)$pdo->query("SELECT COUNT(*) FROM chapters")->fetchColumn(),
    'students' => (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn(),
    'premium_students' => (int)$pdo->query("
        SELECT COUNT(DISTINCT student_id)
        FROM student_subscriptions
        WHERE status = 'active' AND (ends_at IS NULL OR ends_at >= NOW())
    ")->fetchColumn(),
    'active_ads' => (int)$pdo->query("SELECT COUNT(*) FROM ads WHERE is_active = 1")->fetchColumn(),
    'plans' => (int)$pdo->query("SELECT COUNT(*) FROM plans WHERE is_active = 1")->fetchColumn(),
    'pending_payments' => (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status IN ('requested', 'paid')")->fetchColumn(),
    'unread_msgs' => (int)$pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn(),
    'quiz_attempts' => (int)$pdo->query("SELECT COUNT(*) FROM student_quiz_attempts")->fetchColumn(),
];

$completionRate = 0;
if ($stats['chapters'] > 0 && $stats['students'] > 0) {
    $completed = (int)$pdo->query("SELECT COUNT(*) FROM student_progress")->fetchColumn();
    $completionRate = min(100, round(($completed / max(1, $stats['chapters'] * $stats['students'])) * 100));
}

$recentMessages = $pdo->query("SELECT * FROM messages ORDER BY is_read ASC, created_at DESC LIMIT 5")->fetchAll();
$recentStudents = $pdo->query("
    SELECT s.id, s.name, s.email, s.account_status, s.created_at,
           p.name AS plan_name,
           sub.ends_at
    FROM students s
    LEFT JOIN student_subscriptions sub ON sub.id = (
        SELECT ss.id
        FROM student_subscriptions ss
        WHERE ss.student_id = s.id AND ss.status = 'active' AND (ss.ends_at IS NULL OR ss.ends_at >= NOW())
        ORDER BY ss.ends_at DESC, ss.id DESC
        LIMIT 1
    )
    LEFT JOIN plans p ON p.id = sub.plan_id
    ORDER BY s.id DESC
    LIMIT 6
")->fetchAll();
$pendingPayments = $pdo->query("
    SELECT pay.*, s.name AS student_name, s.email AS student_email, p.name AS plan_name
    FROM payments pay
    LEFT JOIN students s ON s.id = pay.student_id
    LEFT JOIN plans p ON p.id = pay.plan_id
    WHERE pay.status IN ('requested', 'paid')
    ORDER BY pay.created_at DESC
    LIMIT 5
")->fetchAll();
$editorStats = [
    'published_chapters' => (int)$pdo->query("SELECT COUNT(*) FROM chapters")->fetchColumn(),
    'draft_chapters' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE editorial_status = 'draft'")->fetchColumn(),
    'in_review_chapters' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE editorial_status = 'in_review'")->fetchColumn(),
    'live_chapters' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE editorial_status = 'published'")->fetchColumn(),
    'chapters_with_quiz' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE quiz_json IS NOT NULL AND quiz_json != ''")->fetchColumn(),
    'chapters_with_practice' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE practice_content IS NOT NULL AND TRIM(practice_content) != ''")->fetchColumn(),
    'chapters_with_download' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE download_file IS NOT NULL AND TRIM(download_file) != ''")->fetchColumn(),
    'chapters_updated_7d' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
];
$editorQueues = [
    'needs_quiz' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE quiz_json IS NULL OR quiz_json = ''")->fetchColumn(),
    'needs_practice' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE practice_content IS NULL OR TRIM(practice_content) = ''")->fetchColumn(),
    'needs_download' => (int)$pdo->query("SELECT COUNT(*) FROM chapters WHERE download_file IS NULL OR TRIM(download_file) = ''")->fetchColumn(),
];
$recentEditorialChapters = $pdo->query("
    SELECT ch.id, ch.chapter_name, ch.order_index, ch.created_at, ch.editorial_status, c.title AS course_title, c.slug AS course_slug, c.id AS course_id
    FROM chapters ch
    INNER JOIN courses c ON c.id = ch.course_id
    ORDER BY ch.id DESC
    LIMIT 8
")->fetchAll();

$notificationStats = [
    'total' => (int)$pdo->query("SELECT COUNT(*) FROM student_notifications")->fetchColumn(),
    'unread' => (int)$pdo->query("SELECT COUNT(*) FROM student_notifications WHERE is_read = 0")->fetchColumn(),
    'recommendation' => (int)$pdo->query("SELECT COUNT(*) FROM student_notifications WHERE type = 'recommendation'")->fetchColumn(),
    'delivery_sent' => (int)$pdo->query("SELECT COUNT(*) FROM notification_delivery_logs WHERE status = 'sent'")->fetchColumn(),
    'delivery_failed' => (int)$pdo->query("SELECT COUNT(*) FROM notification_delivery_logs WHERE status = 'failed'")->fetchColumn(),
    'email_sent' => (int)$pdo->query("SELECT COUNT(*) FROM notification_delivery_logs WHERE channel = 'email' AND status = 'sent'")->fetchColumn(),
    'digest_sent' => (int)$pdo->query("SELECT COUNT(*) FROM notification_delivery_logs WHERE channel = 'digest' AND status = 'sent'")->fetchColumn(),
];
$topNotifiedStudents = $pdo->query("
    SELECT s.name, s.email, COUNT(sn.id) AS notifications_count,
           SUM(CASE WHEN sn.is_read = 1 THEN 1 ELSE 0 END) AS read_count
    FROM student_notifications sn
    INNER JOIN students s ON s.id = sn.student_id
    GROUP BY sn.student_id
    ORDER BY notifications_count DESC
    LIMIT 6
")->fetchAll();
$deliveryTrend = $pdo->query("
    SELECT DATE(created_at) AS day,
           SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) AS sent_count,
           SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) AS failed_count
    FROM notification_delivery_logs
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY day DESC
")->fetchAll();

$plansEnabled = plans_enabled($pdo);
$trafficStats = cached_visitor_metrics_summary($pdo, 60);
$trafficTrend = cached_visitor_trend_last_days($pdo, 7, 120);
$trafficDistribution = cached_visitor_distribution_summary($pdo, 180);
$trafficHourly = cached_visitor_hourly_trend_today($pdo, 120);
$trafficEngagement = cached_visitor_engagement_estimates($pdo, 180);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .icon-box { width: 2.6rem; height: 2.6rem; border-radius: .85rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    </style>
    <?php echo admin_styles(); ?>
</head>
<body class="bg-slate-50 font-sans antialiased h-screen overflow-hidden text-slate-800">
    <div class="flex h-full">
        <?php echo admin_sidebar($pdo, 'dashboard'); ?>

        <main class="flex-1 overflow-y-auto">
            <header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200">
                <div class="px-8 py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="text-sm font-black text-blue-600 uppercase tracking-widest">Dashboard</div>
                        <h1 class="text-3xl font-black text-slate-950 tracking-tight">Admin Overview</h1>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="courses" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl font-black shadow-sm"><?php echo in_array($adminRole, ['owner', 'editor'], true) ? 'Create Course' : 'Open Courses'; ?></a>
                        <?php if($adminRole === 'owner'): ?>
                            <a href="students" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 rounded-xl font-black shadow-sm">Manage Plans</a>
                        <?php endif; ?>
                        <?php if(in_array($adminRole, ['owner', 'reviewer'], true)): ?>
                            <a href="review_queue" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-3 rounded-xl font-black shadow-sm">Review Queue</a>
                        <?php endif; ?>
                        <a href="../index" target="_blank" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-5 py-3 rounded-xl font-black shadow-sm">View Site</a>
                    </div>
                </div>
            </header>

            <div class="p-8 space-y-8">
                <?php if($digestFlash): ?>
                    <div class="admin-card p-4 border-emerald-200 bg-emerald-50 text-emerald-900 font-bold"><?php echo h($digestFlash); ?></div>
                <?php endif; ?>
                <?php if($stats['pending_payments'] > 0): ?>
                    <a href="students" class="admin-card block p-5 border-amber-200 bg-amber-50">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <div class="text-sm font-black text-amber-700 uppercase tracking-widest">Action Needed</div>
                                <div class="text-xl font-black text-amber-950 mt-1"><?php echo number_format($stats['pending_payments']); ?> payment request(s) need review</div>
                            </div>
                            <span class="bg-amber-500 text-white px-4 py-2 rounded-lg font-black">Open Payments</span>
                        </div>
                    </a>
                <?php endif; ?>

                <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
                    <?php
                    $cards = [
                        ['Courses', $stats['courses'], 'Chapters: ' . number_format($stats['chapters']), 'bg-blue-50 text-blue-700'],
                        ['Students', $stats['students'], 'Premium: ' . number_format($stats['premium_students']), 'bg-emerald-50 text-emerald-700'],
                        ['Plans & Ads', $stats['plans'], 'Active ads: ' . number_format($stats['active_ads']), 'bg-violet-50 text-violet-700'],
                        ['Quiz Attempts', $stats['quiz_attempts'], 'Completion rate: ' . $completionRate . '%', 'bg-amber-50 text-amber-700'],
                    ];
                    foreach ($cards as $card):
                    ?>
                        <div class="admin-card p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-xs font-black text-slate-400 uppercase tracking-widest"><?php echo h($card[0]); ?></div>
                                    <div class="text-4xl font-black text-slate-950 mt-2"><?php echo number_format((int)$card[1]); ?></div>
                                    <div class="text-sm font-bold text-slate-500 mt-2"><?php echo h($card[2]); ?></div>
                                </div>
                                <div class="icon-box <?php echo h($card[3]); ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8m12 0a8 8 0 11-16 0 8 8 0 0116 0z"></path></svg>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>

                <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="admin-card p-6 xl:col-span-1">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-xl font-black text-slate-950">Quick Actions</h2>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Tools</span>
                        </div>
                        <div class="grid grid-cols-1 gap-3">
                            <a href="courses" class="group rounded-xl border border-slate-200 p-4 hover:border-blue-300 hover:bg-blue-50 transition">
                                <div class="font-black text-slate-900 group-hover:text-blue-700">Manage Courses & Chapters</div>
                                <div class="text-sm text-slate-500 mt-1">Create lessons, quizzes, practice sets, and downloads.</div>
                            </a>
                        <?php if(in_array($adminRole, ['owner', 'reviewer'], true)): ?>
                            <a href="review_queue" class="group rounded-xl border border-slate-200 p-4 hover:border-amber-300 hover:bg-amber-50 transition">
                                <div class="font-black text-slate-900 group-hover:text-amber-700">Review Queue</div>
                                <div class="text-sm text-slate-500 mt-1">Approve ready chapters or send fixes back to editors.</div>
                            </a>
                        <?php endif; ?>
                        <?php if($adminRole === 'owner'): ?>
                            <a href="students" class="group rounded-xl border border-slate-200 p-4 hover:border-emerald-300 hover:bg-emerald-50 transition">
                                <div class="font-black text-slate-900 group-hover:text-emerald-700">Users, Plans & Payments</div>
                                <div class="text-sm text-slate-500 mt-1">Approve payments, apply plans, and control access.</div>
                            </a>
                        <?php endif; ?>
                        <?php if(in_array($adminRole, ['owner', 'editor'], true)): ?>
                            <a href="ads" class="group rounded-xl border border-slate-200 p-4 hover:border-violet-300 hover:bg-violet-50 transition">
                                <div class="font-black text-slate-900 group-hover:text-violet-700">Content Ads</div>
                                <div class="text-sm text-slate-500 mt-1">Create responsive ads inside lesson content only.</div>
                            </a>
                        <?php endif; ?>
                        </div>
                    </div>

                    <div class="admin-card overflow-hidden xl:col-span-2">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-xl font-black text-slate-950">Payment Queue</h2>
                            <a href="students" class="text-sm font-black text-blue-600 hover:underline">View all</a>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <?php foreach($pendingPayments as $payment): ?>
                                <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div>
                                        <div class="font-black text-slate-900"><?php echo h($payment['student_name'] ?: 'Unknown student'); ?></div>
                                        <div class="text-sm text-slate-500"><?php echo h($payment['student_email'] ?? ''); ?></div>
                                    </div>
                                    <div class="text-sm">
                                        <div class="font-black text-slate-900"><?php echo h($payment['plan_name'] ?: 'Plan'); ?></div>
                                        <div class="text-slate-500"><?php echo h($payment['transaction_ref'] ?: 'No reference'); ?></div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-black text-slate-950"><?php echo h($payment['currency']); ?> <?php echo number_format((float)$payment['amount'], 2); ?></div>
                                        <div class="text-xs font-black text-amber-600 uppercase"><?php echo h($payment['status']); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if(!$pendingPayments): ?>
                                <div class="p-8 text-center text-slate-500">
                                    <div class="font-black text-slate-900">No payment requests waiting.</div>
                                    <div class="text-sm mt-1">Everything is clear right now.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="admin-card p-6 xl:col-span-1">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-xl font-black text-slate-950">Editor Cockpit</h2>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Content Ops</span>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-slate-500">Published chapters</span><span class="font-black text-slate-900"><?php echo number_format($editorStats['published_chapters']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Draft</span><span class="font-black text-slate-700"><?php echo number_format($editorStats['draft_chapters']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">In review</span><span class="font-black text-amber-700"><?php echo number_format($editorStats['in_review_chapters']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Published workflow</span><span class="font-black text-emerald-700"><?php echo number_format($editorStats['live_chapters']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">With quiz sets</span><span class="font-black text-blue-700"><?php echo number_format($editorStats['chapters_with_quiz']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">With practice sets</span><span class="font-black text-emerald-700"><?php echo number_format($editorStats['chapters_with_practice']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">With downloads</span><span class="font-black text-violet-700"><?php echo number_format($editorStats['chapters_with_download']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Created in last 7 days</span><span class="font-black text-amber-700"><?php echo number_format($editorStats['chapters_updated_7d']); ?></span></div>
                        </div>
                        <div class="mt-5 grid grid-cols-1 gap-2">
                            <a href="courses" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-50">Open course manager</a>
                            <?php if(in_array($adminRole, ['owner', 'editor'], true)): ?>
                                <a href="add_course" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-black text-blue-700 hover:bg-blue-100">Create new course</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="admin-card overflow-hidden xl:col-span-2">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-xl font-black text-slate-950">Editorial Queue</h2>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Fix Coverage Gaps</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <div class="text-xs font-black uppercase tracking-widest text-amber-700">Need Quiz</div>
                                <div class="text-3xl font-black text-amber-900 mt-2"><?php echo number_format($editorQueues['needs_quiz']); ?></div>
                                <div class="text-xs text-amber-700 mt-1">Chapters without assessment</div>
                            </div>
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                                <div class="text-xs font-black uppercase tracking-widest text-emerald-700">Need Practice</div>
                                <div class="text-3xl font-black text-emerald-900 mt-2"><?php echo number_format($editorQueues['needs_practice']); ?></div>
                                <div class="text-xs text-emerald-700 mt-1">Missing hands-on exercises</div>
                            </div>
                            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                                <div class="text-xs font-black uppercase tracking-widest text-indigo-700">Need Download</div>
                                <div class="text-3xl font-black text-indigo-900 mt-2"><?php echo number_format($editorQueues['needs_download']); ?></div>
                                <div class="text-xs text-indigo-700 mt-1">No worksheet/resource attached</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div class="admin-card overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-xl font-black text-slate-950">Recent Students</h2>
                            <a href="students" class="text-sm font-black text-blue-600 hover:underline">Manage</a>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <?php foreach($recentStudents as $student): ?>
                                <div class="p-5 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 truncate"><?php echo h($student['name']); ?></div>
                                        <div class="text-sm text-slate-500 truncate"><?php echo h($student['email']); ?></div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <div class="text-sm font-black <?php echo $student['plan_name'] ? 'text-emerald-700' : 'text-slate-500'; ?>"><?php echo h($student['plan_name'] ?: 'Free'); ?></div>
                                        <div class="text-xs text-slate-400"><?php echo h(ucfirst($student['account_status'] ?? 'active')); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="admin-card overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-xl font-black text-slate-950">Messages</h2>
                            <a href="messages" class="text-sm font-black text-blue-600 hover:underline">Open inbox</a>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <?php foreach($recentMessages as $msg): ?>
                                <div class="p-5 flex items-start justify-between gap-4 <?php echo (int)$msg['is_read'] === 0 ? 'bg-blue-50/40' : ''; ?>">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <?php if((int)$msg['is_read'] === 0): ?><span class="w-2 h-2 bg-blue-600 rounded-full"></span><?php endif; ?>
                                            <div class="font-black text-slate-900 truncate"><?php echo h($msg['subject'] ?: 'No subject'); ?></div>
                                        </div>
                                        <div class="text-sm text-slate-500 truncate mt-1"><?php echo h($msg['name']); ?> - <?php echo h($msg['email']); ?></div>
                                    </div>
                                    <div class="text-xs font-bold text-slate-400 whitespace-nowrap"><?php echo h(date('M j', strtotime($msg['created_at']))); ?></div>
                                </div>
                            <?php endforeach; ?>
                            <?php if(!$recentMessages): ?>
                                <div class="p-8 text-center text-slate-500">No messages yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <section class="admin-card overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-xl font-black text-slate-950">Recently Edited Chapters</h2>
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Editor shortcuts</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <?php foreach($recentEditorialChapters as $row): ?>
                            <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-black text-slate-900 truncate"><?php echo h($row['chapter_name']); ?></div>
                                    <div class="text-sm text-slate-500 truncate"><?php echo h($row['course_title']); ?> - Lesson <?php echo (int)$row['order_index']; ?></div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] uppercase tracking-widest font-black px-2 py-1 rounded-full border <?php echo ($row['editorial_status'] ?? 'draft') === 'published' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : (($row['editorial_status'] ?? 'draft') === 'in_review' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-slate-50 text-slate-700 border-slate-100'); ?>">
                                        <?php echo h((string)($row['editorial_status'] ?? 'draft')); ?>
                                    </span>
                                    <a href="chapters?course_id=<?php echo (int)$row['course_id']; ?>&edit=<?php echo (int)$row['id']; ?>" class="text-sm font-black text-blue-600 hover:underline">Edit content</a>
                                    <a href="../<?php echo h(course_path((string)$row['course_slug'])); ?>" target="_blank" class="text-sm font-black text-slate-600 hover:underline">Preview</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if(!$recentEditorialChapters): ?>
                            <div class="p-8 text-center text-slate-500">No chapters available for editing yet.</div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="admin-card p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">System Status</h2>
                            <p class="text-sm text-slate-500 mt-1">Important switches and platform health at a glance.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full lg:w-auto">
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="text-xs font-black uppercase tracking-widest text-slate-400">Plans</div>
                                <div class="font-black mt-1 <?php echo $plansEnabled ? 'text-emerald-700' : 'text-amber-700'; ?>"><?php echo $plansEnabled ? 'Enabled' : 'Disabled'; ?></div>
                            </div>
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="text-xs font-black uppercase tracking-widest text-slate-400">Ads</div>
                                <div class="font-black mt-1 text-slate-900"><?php echo number_format($stats['active_ads']); ?> active</div>
                            </div>
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="text-xs font-black uppercase tracking-widest text-slate-400">Inbox</div>
                                <div class="font-black mt-1 <?php echo $stats['unread_msgs'] > 0 ? 'text-blue-700' : 'text-slate-900'; ?>"><?php echo number_format($stats['unread_msgs']); ?> unread</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="admin-card p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xl font-black text-slate-950">Traffic Snapshot</h2>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="index?export=traffic_daily" class="text-xs font-black px-2.5 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">Export Daily CSV</a>
                            <a href="index?export=traffic_hourly" class="text-xs font-black px-2.5 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">Export Hourly CSV</a>
                            <a href="index?export=traffic_distribution" class="text-xs font-black px-2.5 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">Export Distribution CSV</a>
                            <a href="index?export=traffic_engagement" class="text-xs font-black px-2.5 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">Export Engagement CSV</a>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                            <div class="text-xs font-black uppercase tracking-widest text-blue-600">Lifetime</div>
                            <div class="text-2xl font-black text-slate-900 mt-1"><?php echo number_format((int)$trafficStats['lifetime']); ?></div>
                        </div>
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                            <div class="text-xs font-black uppercase tracking-widest text-emerald-600">Today Unique</div>
                            <div class="text-2xl font-black text-slate-900 mt-1"><?php echo number_format((int)$trafficStats['today_unique']); ?></div>
                        </div>
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                            <div class="text-xs font-black uppercase tracking-widest text-indigo-600">Month Unique</div>
                            <div class="text-2xl font-black text-slate-900 mt-1"><?php echo number_format((int)$trafficStats['month_unique']); ?></div>
                        </div>
                        <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">
                            <div class="text-xs font-black uppercase tracking-widest text-amber-600">Live Online (5m)</div>
                            <div class="text-2xl font-black text-slate-900 mt-1"><?php echo number_format((int)$trafficStats['live_online']); ?></div>
                        </div>
                    </div>

                    <?php
                        $maxUnique = 0;
                        foreach ($trafficTrend as $point) {
                            $maxUnique = max($maxUnique, (int)$point['unique_visitors']);
                        }
                        $maxUnique = max(1, $maxUnique);
                    ?>
                    <div class="mt-6">
                        <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest mb-3">7 Day Unique Trend</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-7 gap-2">
                            <?php foreach($trafficTrend as $point): ?>
                                <?php
                                    $heightPercent = (int)round(((int)$point['unique_visitors'] / $maxUnique) * 100);
                                    $heightPercent = max(6, $heightPercent);
                                ?>
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
                                    <div class="h-24 flex items-end">
                                        <div class="w-full bg-blue-500 rounded-md" style="height: <?php echo $heightPercent; ?>%"></div>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-bold mt-2"><?php echo h(date('D', strtotime((string)$point['date']))); ?></div>
                                    <div class="text-xs font-black text-slate-900"><?php echo number_format((int)$point['unique_visitors']); ?></div>
                                    <div class="text-[10px] text-slate-400">hits <?php echo number_format((int)$point['total_hits']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php
                        $maxHourlyHits = 0;
                        foreach ($trafficHourly as $point) {
                            $maxHourlyHits = max($maxHourlyHits, (int)$point['hits']);
                        }
                        $maxHourlyHits = max(1, $maxHourlyHits);
                    ?>
                    <div class="mt-6">
                        <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest mb-3">Today Hourly Hits</h3>
                        <div class="grid grid-cols-4 sm:grid-cols-6 xl:grid-cols-12 gap-2">
                            <?php foreach($trafficHourly as $point): ?>
                                <?php
                                    $heightPercent = (int)round(((int)$point['hits'] / $maxHourlyHits) * 100);
                                    $heightPercent = max(4, $heightPercent);
                                ?>
                                <div class="rounded-lg border border-slate-200 bg-white p-2">
                                    <div class="h-14 flex items-end">
                                        <div class="w-full bg-indigo-500 rounded" style="height: <?php echo $heightPercent; ?>%"></div>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-bold mt-1"><?php echo h(substr((string)$point['hour'], 0, 2)); ?>h</div>
                                    <div class="text-[10px] text-slate-700 font-black"><?php echo number_format((int)$point['hits']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                        <div class="rounded-xl border border-rose-100 bg-rose-50 p-4">
                            <div class="text-xs font-black uppercase tracking-widest text-rose-600">Bounce Overall</div>
                            <div class="text-xl font-black text-slate-900 mt-1"><?php echo h((string)$trafficEngagement['bounce_rate_overall']); ?>%</div>
                        </div>
                        <div class="rounded-xl border border-orange-100 bg-orange-50 p-4">
                            <div class="text-xs font-black uppercase tracking-widest text-orange-600">Bounce Today</div>
                            <div class="text-xl font-black text-slate-900 mt-1"><?php echo h((string)$trafficEngagement['bounce_rate_today']); ?>%</div>
                        </div>
                        <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">
                            <div class="text-xs font-black uppercase tracking-widest text-sky-600">Avg Hits/Visitor</div>
                            <div class="text-xl font-black text-slate-900 mt-1"><?php echo h((string)$trafficEngagement['avg_hits_per_visitor']); ?></div>
                        </div>
                        <div class="rounded-xl border border-lime-100 bg-lime-50 p-4">
                            <div class="text-xs font-black uppercase tracking-widest text-lime-600">Avg Session Min</div>
                            <div class="text-xl font-black text-slate-900 mt-1"><?php echo h((string)$trafficEngagement['avg_session_minutes']); ?></div>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="admin-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Top Countries</h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <?php foreach(($trafficDistribution['countries'] ?? []) as $row): ?>
                                <div class="px-6 py-3 flex items-center justify-between text-sm">
                                    <span class="font-black text-slate-800"><?php echo h((string)$row['label']); ?></span>
                                    <span class="text-slate-500"><?php echo number_format((int)$row['count']); ?></span>
                                </div>
                            <?php endforeach; ?>
                            <?php if(empty($trafficDistribution['countries'])): ?><div class="px-6 py-6 text-sm text-slate-500">No data yet.</div><?php endif; ?>
                        </div>
                    </div>
                    <div class="admin-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Top Devices</h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <?php foreach(($trafficDistribution['devices'] ?? []) as $row): ?>
                                <div class="px-6 py-3 flex items-center justify-between text-sm">
                                    <span class="font-black text-slate-800"><?php echo h(ucfirst((string)$row['label'])); ?></span>
                                    <span class="text-slate-500"><?php echo number_format((int)$row['count']); ?></span>
                                </div>
                            <?php endforeach; ?>
                            <?php if(empty($trafficDistribution['devices'])): ?><div class="px-6 py-6 text-sm text-slate-500">No data yet.</div><?php endif; ?>
                        </div>
                    </div>
                    <div class="admin-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Top Browsers</h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <?php foreach(($trafficDistribution['browsers'] ?? []) as $row): ?>
                                <div class="px-6 py-3 flex items-center justify-between text-sm">
                                    <span class="font-black text-slate-800"><?php echo h(ucfirst((string)$row['label'])); ?></span>
                                    <span class="text-slate-500"><?php echo number_format((int)$row['count']); ?></span>
                                </div>
                            <?php endforeach; ?>
                            <?php if(empty($trafficDistribution['browsers'])): ?><div class="px-6 py-6 text-sm text-slate-500">No data yet.</div><?php endif; ?>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="admin-card p-6 xl:col-span-1">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-xl font-black text-slate-950">Notification Analytics</h2>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">7d+</span>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-slate-500">Total notifications</span><span class="font-black text-slate-900"><?php echo number_format($notificationStats['total']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Unread</span><span class="font-black text-blue-700"><?php echo number_format($notificationStats['unread']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Recommendation type</span><span class="font-black text-indigo-700"><?php echo number_format($notificationStats['recommendation']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Deliveries sent</span><span class="font-black text-emerald-700"><?php echo number_format($notificationStats['delivery_sent']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Deliveries failed</span><span class="font-black text-red-700"><?php echo number_format($notificationStats['delivery_failed']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Email sent</span><span class="font-black text-slate-900"><?php echo number_format($notificationStats['email_sent']); ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Digest sent</span><span class="font-black text-violet-700"><?php echo number_format($notificationStats['digest_sent']); ?></span></div>
                        </div>
                        <form method="POST" action="index" class="mt-5">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="run_digest_now">
                            <button type="submit" class="w-full admin-btn-primary">Run Digests Now</button>
                        </form>
                        <p class="text-xs text-slate-500 mt-3">For automation, trigger <code>notifications_digest_runner?token=YOUR_TOKEN</code> from cron/task scheduler.</p>
                    </div>

                    <div class="admin-card overflow-hidden xl:col-span-2">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-xl font-black text-slate-950">Top Notified Students</h2>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Engagement</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <?php foreach($topNotifiedStudents as $row): ?>
                                <?php $readRate = (int)$row['notifications_count'] > 0 ? round(((int)$row['read_count'] / (int)$row['notifications_count']) * 100) : 0; ?>
                                <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div>
                                        <div class="font-black text-slate-900"><?php echo h($row['name']); ?></div>
                                        <div class="text-sm text-slate-500"><?php echo h($row['email']); ?></div>
                                    </div>
                                    <div class="text-sm text-slate-600">
                                        <div><span class="font-black text-slate-900"><?php echo number_format((int)$row['notifications_count']); ?></span> notifications</div>
                                        <div><span class="font-black text-emerald-700"><?php echo $readRate; ?>%</span> read rate</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if(!$topNotifiedStudents): ?>
                                <div class="p-8 text-center text-slate-500">No notification activity yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <section class="admin-card overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-xl font-black text-slate-950">Delivery Trend (Last 7 Days)</h2>
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Sent vs Failed</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <?php foreach($deliveryTrend as $trend): ?>
                            <div class="p-5 flex items-center justify-between gap-4">
                                <div class="font-black text-slate-900"><?php echo h(date('M j, Y', strtotime((string)$trend['day']))); ?></div>
                                <div class="flex items-center gap-6 text-sm">
                                    <div class="text-emerald-700 font-black">Sent: <?php echo number_format((int)$trend['sent_count']); ?></div>
                                    <div class="text-red-700 font-black">Failed: <?php echo number_format((int)$trend['failed_count']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if(!$deliveryTrend): ?>
                            <div class="p-8 text-center text-slate-500">No delivery logs available in last 7 days.</div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
