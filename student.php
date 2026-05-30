<?php
require_once 'includes/db.php';
secure_session_start();

if (empty($_SESSION['student_logged_in'])) {
    redirect('login');
}

$student_id = (int)$_SESSION['student_id'];
$subscription = active_subscription($pdo, $student_id);
maybe_emit_personalized_recommendation($pdo, $student_id);
$personalized_recommendations = recommended_courses_for_student($pdo, $student_id, 6);

$stmtCourses = $pdo->prepare("
    SELECT
        c.id,
        c.title,
        c.slug,
        c.description,
        COUNT(DISTINCT ch.id) AS total_chapters,
        COUNT(DISTINCT sp.chapter_id) AS completed_chapters,
        MAX(sp.id) AS last_activity
    FROM courses c
    LEFT JOIN chapters ch ON ch.course_id = c.id
    LEFT JOIN student_progress sp ON sp.course_id = c.id AND sp.chapter_id = ch.id AND sp.student_id = :student_id
    GROUP BY c.id
    ORDER BY last_activity DESC, c.created_at DESC
");
$stmtCourses->execute(['student_id' => $student_id]);
$courses = $stmtCourses->fetchAll();

$total_courses = count($courses);
$started_courses = 0;
$completed_courses = 0;
foreach ($courses as $course) {
    if ((int)$course['completed_chapters'] > 0) {
        $started_courses++;
    }
    if ((int)$course['total_chapters'] > 0 && (int)$course['completed_chapters'] >= (int)$course['total_chapters']) {
        $completed_courses++;
    }
}
$activity = student_activity_summary($pdo, $student_id);
$notifications = recent_notifications($pdo, $student_id, 12);

$seo_title = 'My Learning Panel';
$seo_description = 'Track courses, completed lessons, and continue learning from your student dashboard.';
$seo_canonical = site_base_url() . 'student';
$seo_type = 'website';
$seo_robots = 'noindex, nofollow';
$seo_keywords = 'student dashboard, learning progress, course notifications';
require_once 'includes/header.php';
?>

<section class="site-hero-light py-14 border-b border-blue-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <span class="edu-pill mb-4">Student Panel</span>
                <h1 class="text-4xl md:text-5xl font-black text-slate-950 tracking-tight">Welcome, <?php echo h($_SESSION['student_name']); ?></h1>
                <p class="text-slate-600 mt-3 max-w-2xl">Continue lessons, track completion, and keep your learning organized in one place.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="billing" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition text-center"><?php echo $subscription ? 'Manage Premium' : 'Upgrade Plan'; ?></a>
                <a href="profile" class="bg-white text-slate-900 hover:bg-blue-50 font-bold py-3 px-6 rounded-lg shadow-lg transition text-center">Profile & QR Card</a>
                <a href="index#courses" class="edu-primary-btn py-3 px-6 transition text-center">Browse Courses</a>
            </div>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-5 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-6 learning-surface">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400">Available Courses</p>
            <div class="text-3xl font-black text-slate-900 mt-2"><?php echo number_format($total_courses); ?></div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 learning-surface">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400">Started</p>
            <div class="text-3xl font-black text-blue-600 mt-2"><?php echo number_format($started_courses); ?></div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 learning-surface">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400">Completed</p>
            <div class="text-3xl font-black text-emerald-600 mt-2"><?php echo number_format($completed_courses); ?></div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 learning-surface">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400">Plan</p>
            <div class="text-xl font-black <?php echo $subscription ? 'text-emerald-700' : 'text-slate-900'; ?> mt-2"><?php echo $subscription ? h($subscription['plan_name']) : 'Free'; ?></div>
            <p class="text-xs text-slate-500 mt-2"><?php echo $subscription ? 'Ads hidden for your account.' : 'Upgrade for ad-free learning.'; ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 learning-surface">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400">Quiz Report</p>
            <div class="text-3xl font-black text-amber-600 mt-2"><?php echo h((string)$activity['average_quiz_score']); ?>%</div>
            <p class="text-xs text-slate-500 mt-2"><?php echo (int)$activity['quizzes_passed']; ?> passed / <?php echo (int)$activity['quiz_attempts']; ?> attempts</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 learning-surface overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-900">Learning Report & Resume</h2>
            <a href="profile" class="text-sm font-bold text-blue-600 hover:underline">Update public resume</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-6">
            <div class="rounded-lg bg-blue-50 border border-blue-100 p-5">
                <p class="text-xs font-black uppercase tracking-widest text-blue-500">Resume Signal</p>
                <p class="text-sm text-blue-950 mt-2">Your public profile now includes completed lessons, courses, quiz score, and passed quizzes.</p>
            </div>
            <div class="rounded-lg bg-emerald-50 border border-emerald-100 p-5">
                <p class="text-xs font-black uppercase tracking-widest text-emerald-500">Practice Sets</p>
                <p class="text-sm text-emerald-950 mt-2">Practice tasks appear inside chapters when admin adds them.</p>
            </div>
            <div class="rounded-lg bg-amber-50 border border-amber-100 p-5">
                <p class="text-xs font-black uppercase tracking-widest text-amber-500">Downloads</p>
                <p class="text-sm text-amber-950 mt-2">Files appear in chapters when admin enables downloads; premium files require an active plan.</p>
            </div>
        </div>
    </div>

    <div id="notification-center" class="bg-white rounded-xl border border-gray-200 learning-surface overflow-hidden mb-8 scroll-mt-28">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-900">Notification Center</h2>
            <a href="recommendations-feed.json" class="text-sm font-bold text-indigo-600 hover:underline">Recommendation feed</a>
        </div>
        <div class="divide-y divide-gray-100">
            <?php foreach($notifications as $notification): ?>
                <article class="px-6 py-4 <?php echo (int)$notification['is_read'] === 1 ? 'bg-white' : 'bg-blue-50/40'; ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-black text-slate-900"><?php echo h($notification['title']); ?></h3>
                            <p class="text-sm text-slate-600 mt-1 leading-6"><?php echo h($notification['message']); ?></p>
                            <p class="text-xs text-slate-400 mt-2"><?php echo h(date('d M Y, h:i A', strtotime((string)$notification['created_at']))); ?></p>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wide px-2 py-1 rounded-full border <?php echo $notification['type'] === 'recommendation' ? 'text-indigo-700 bg-indigo-50 border-indigo-100' : ($notification['type'] === 'progress' ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : 'text-slate-700 bg-slate-50 border-slate-100'); ?>">
                            <?php echo h($notification['type']); ?>
                        </span>
                    </div>
                    <?php if (($notification['type'] ?? '') === 'recommendation' && !empty($notification['meta']['reason_details']) && is_array($notification['meta']['reason_details'])): ?>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <?php foreach (array_slice($notification['meta']['reason_details'], 0, 3) as $detail): ?>
                                <span class="text-[11px] font-bold text-indigo-700 bg-white border border-indigo-100 rounded-md px-2 py-1"><?php echo h((string)$detail); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($notification['action_url'])): ?>
                        <a href="<?php echo h((string)$notification['action_url']); ?>" class="inline-flex mt-3 text-sm font-bold text-blue-600 hover:underline">Open recommendation</a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
            <?php if (empty($notifications)): ?>
                <div class="px-6 py-10 text-sm text-slate-500 text-center">No notifications yet. Complete lessons to receive recommendations.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php if(!empty($personalized_recommendations)): ?>
        <div class="bg-white rounded-xl border border-gray-200 learning-surface overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-xl font-black text-slate-900">Personalized Recommendations</h2>
                <a href="profile" class="text-sm font-bold text-blue-600 hover:underline">Tune notification preferences</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                <?php foreach($personalized_recommendations as $item): ?>
                    <a href="<?php echo h(course_path((string)$item['slug'])); ?>" class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-5 hover:border-indigo-300 transition">
                        <p class="text-[10px] uppercase tracking-widest font-black text-indigo-500 mb-2">Recommended</p>
                        <h3 class="text-lg font-black text-slate-900"><?php echo h((string)$item['title']); ?></h3>
                        <p class="text-sm text-slate-600 mt-2 leading-6"><?php echo h((string)$item['recommendation_reason']); ?></p>
                        <?php if (!empty($item['recommendation_reason_details']) && is_array($item['recommendation_reason_details'])): ?>
                            <ul class="mt-3 space-y-1">
                                <?php foreach(array_slice($item['recommendation_reason_details'], 0, 3) as $detail): ?>
                                    <li class="text-xs text-indigo-700 font-bold bg-white border border-indigo-100 rounded-md px-2 py-1"><?php echo h((string)$detail); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <div class="mt-3 text-xs font-black text-indigo-700">Score: <?php echo h((string)$item['recommendation_score']); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 learning-surface overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-900">Your Courses</h2>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Progress Overview</span>
        </div>

        <div class="divide-y divide-gray-100">
            <?php foreach($courses as $course): 
                $total = (int)$course['total_chapters'];
                $done = (int)$course['completed_chapters'];
                $percent = $total > 0 ? round(($done / $total) * 100) : 0;

                $stmtNext = $pdo->prepare("
                    SELECT ch.id
                    FROM chapters ch
                    LEFT JOIN student_progress sp ON sp.chapter_id = ch.id AND sp.student_id = :student_id
                    WHERE ch.course_id = :course_id AND sp.id IS NULL
                    ORDER BY ch.order_index ASC
                    LIMIT 1
                ");
                $stmtNext->execute(['student_id' => $student_id, 'course_id' => $course['id']]);
                $next_chapter_id = (int)($stmtNext->fetchColumn() ?: 0);
                if (!$next_chapter_id) {
                    $stmtFirst = $pdo->prepare("SELECT id FROM chapters WHERE course_id = :course_id ORDER BY order_index ASC LIMIT 1");
                    $stmtFirst->execute(['course_id' => $course['id']]);
                    $next_chapter_id = (int)($stmtFirst->fetchColumn() ?: 0);
                }
                $next_chapter_name = '';
                if ($next_chapter_id) {
                    $stmtNextName = $pdo->prepare("SELECT chapter_name FROM chapters WHERE id = :id AND course_id = :course_id");
                    $stmtNextName->execute(['id' => $next_chapter_id, 'course_id' => $course['id']]);
                    $next_chapter_name = (string)($stmtNextName->fetchColumn() ?: '');
                }
                $course_url = $next_chapter_id ? chapter_path($course['slug'], $next_chapter_id, $next_chapter_name) : course_path($course['slug']);
            ?>
                <article class="p-6 flex flex-col lg:flex-row lg:items-center gap-5">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-black text-slate-900"><?php echo h($course['title']); ?></h3>
                        <p class="text-sm text-slate-500 mt-1 line-clamp-2"><?php echo h($course['description']); ?></p>
                        <div class="mt-4 flex items-center gap-3">
                            <div class="flex-1 max-w-md bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $percent; ?>%"></div>
                            </div>
                            <span class="text-xs font-black text-slate-500"><?php echo $done; ?>/<?php echo $total; ?> lessons</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-black <?php echo $percent === 100 ? 'text-emerald-600' : 'text-blue-600'; ?>"><?php echo $percent; ?>%</span>
                        <a href="<?php echo h($course_url); ?>" class="edu-primary-btn py-2.5 px-5 transition"><?php echo $done > 0 ? 'Continue' : 'Start'; ?></a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if(empty($courses)): ?>
                <div class="p-12 text-center text-slate-500">
                    <h3 class="text-xl font-black text-slate-900">No courses available yet</h3>
                    <p class="mt-2">New courses will appear here automatically.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
