<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    verify_csrf();
    session_destroy();
    header("Location: login");
    exit;
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

$plansEnabled = plans_enabled($pdo);
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
                        <a href="courses" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl font-black shadow-sm">Create Course</a>
                        <a href="students" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 rounded-xl font-black shadow-sm">Manage Plans</a>
                        <a href="../index" target="_blank" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-5 py-3 rounded-xl font-black shadow-sm">View Site</a>
                    </div>
                </div>
            </header>

            <div class="p-8 space-y-8">
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
                            <a href="students" class="group rounded-xl border border-slate-200 p-4 hover:border-emerald-300 hover:bg-emerald-50 transition">
                                <div class="font-black text-slate-900 group-hover:text-emerald-700">Users, Plans & Payments</div>
                                <div class="text-sm text-slate-500 mt-1">Approve payments, apply plans, and control access.</div>
                            </a>
                            <a href="ads" class="group rounded-xl border border-slate-200 p-4 hover:border-violet-300 hover:bg-violet-50 transition">
                                <div class="font-black text-slate-900 group-hover:text-violet-700">Content Ads</div>
                                <div class="text-sm text-slate-500 mt-1">Create responsive ads inside lesson content only.</div>
                            </a>
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
            </div>
        </main>
    </div>
</body>
</html>
