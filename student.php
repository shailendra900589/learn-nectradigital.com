<?php
require_once 'includes/db.php';
secure_session_start();

if (empty($_SESSION['student_logged_in'])) {
    redirect('login');
}

$student_id = (int)$_SESSION['student_id'];
$subscription = active_subscription($pdo, $student_id);

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

$studentCopy = [
    'My Learning Panel',
    'Track courses, completed lessons, and continue learning from your student dashboard.',
    'Available Tracks',
    '6 Core Technologies',
    'Tracks Started',
    'Active in progress',
    'Completed',
    'Mastered tracks',
    'Account Plan',
    'Free Student',
    'Ad-free learning enabled.',
    'Free access to all 91 lessons.',
    'Average Quiz Score',
    ((int)$activity['quizzes_passed']) . ' passed / ' . ((int)$activity['quiz_attempts']) . ' attempts',
    'Learning Report & Resume',
    'Resume Signal',
    'Your public profile now includes completed lessons, courses, quiz score, and passed quizzes.',
    'Practice Sets',
    'Practice tasks appear inside chapters when admin adds them.',
    'Downloads',
    'Files appear in chapters when admin enables downloads; premium files require an active plan.',
    'Your Courses',
    'Progress Overview',
    'lessons',
    'No courses available yet',
    'New courses will appear here automatically.',
];
foreach ($courses as $course) {
    $studentCopy[] = (string)$course['title'];
    $studentCopy[] = (string)$course['description'];
}
if (is_array($subscription) && !empty($subscription['plan_name'])) {
    $studentCopy[] = (string)$subscription['plan_name'];
}
if (current_lang() !== 'en') {
    localize_texts($studentCopy);
}
$seo_title = localize_text('My Learning Panel');
$seo_description = localize_text('Track courses, completed lessons, and continue learning from your student dashboard.');
$seo_canonical = site_base_url() . 'student';
$seo_type = 'website';
$seo_robots = 'noindex, nofollow';
require_once 'includes/header.php';
?>

<section class="page-intro text-white py-12 md:py-16 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background: radial-gradient(circle at 70% 30%, #04AA6D 0%, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <?php echo h(t('student.dashboard')); ?>
                </span>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight"><?php echo h(t('student.welcome')); ?> <?php echo h($_SESSION['student_name']); ?>!</h1>
                <p class="text-slate-300 mt-2 max-w-2xl text-sm sm:text-base"><?php echo h(t('student.lead')); ?></p>
            </div>
            <div class="flex flex-wrap sm:flex-nowrap gap-3">
                <a href="<?php echo h(lang_href('billing')); ?>" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg transition text-xs sm:text-sm text-center flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <span><?php echo h($subscription ? t('student.manage') : t('student.upgrade')); ?></span>
                </a>
                <a href="<?php echo h(lang_href('profile')); ?>" class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 font-bold py-2.5 px-5 rounded-xl shadow-sm transition text-xs sm:text-sm text-center flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    <span><?php echo h(t('account.profile')); ?></span>
                </a>
                <a href="<?php echo h(lang_href('index#courses')); ?>" class="bg-white hover:bg-slate-100 text-slate-900 font-bold py-2.5 px-5 rounded-xl transition text-xs sm:text-sm text-center">
                    <?php echo h(t('student.browse')); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400"><?php echo h(localize_text('Available Tracks')); ?></p>
            <div class="text-3xl font-black text-slate-900 mt-2"><?php echo number_format($total_courses); ?></div>
            <p class="text-xs text-slate-500 mt-2"><?php echo h(localize_text('6 Core Technologies')); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <p class="text-xs font-black uppercase tracking-widest text-blue-600"><?php echo h(localize_text('Tracks Started')); ?></p>
            <div class="text-3xl font-black text-blue-600 mt-2"><?php echo number_format($started_courses); ?></div>
            <p class="text-xs text-slate-500 mt-2"><?php echo h(localize_text('Active in progress')); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <p class="text-xs font-black uppercase tracking-widest text-emerald-600"><?php echo h(localize_text('Completed')); ?></p>
            <div class="text-3xl font-black text-emerald-600 mt-2"><?php echo number_format($completed_courses); ?></div>
            <p class="text-xs text-slate-500 mt-2"><?php echo h(localize_text('Mastered tracks')); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400"><?php echo h(localize_text('Account Plan')); ?></p>
            <div class="text-xl font-black <?php echo $subscription ? 'text-emerald-700' : 'text-slate-900'; ?> mt-2"><?php echo $subscription ? h(localize_text((string)$subscription['plan_name'])) : h(localize_text('Free Student')); ?></div>
            <p class="text-xs text-slate-500 mt-2"><?php echo h(localize_text($subscription ? 'Ad-free learning enabled.' : 'Free access to all 91 lessons.')); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <p class="text-xs font-black uppercase tracking-widest text-amber-600"><?php echo h(localize_text('Average Quiz Score')); ?></p>
            <div class="text-3xl font-black text-amber-600 mt-2"><?php echo h((string)$activity['average_quiz_score']); ?>%</div>
            <p class="text-xs text-slate-500 mt-2"><?php echo h(localize_text((int)$activity['quizzes_passed'] . ' passed / ' . (int)$activity['quiz_attempts'] . ' attempts')); ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 learning-surface overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-900"><?php echo h(localize_text('Learning Report & Resume')); ?></h2>
            <a href="<?php echo h(lang_href('profile')); ?>" class="text-sm font-bold text-blue-600 hover:underline"><?php echo h(t('student.resume')); ?></a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-6">
            <div class="rounded-lg bg-blue-50 border border-blue-100 p-5">
                <p class="text-xs font-black uppercase tracking-widest text-blue-500"><?php echo h(localize_text('Resume Signal')); ?></p>
                <p class="text-sm text-blue-950 mt-2"><?php echo h(localize_text('Your public profile now includes completed lessons, courses, quiz score, and passed quizzes.')); ?></p>
            </div>
            <div class="rounded-lg bg-emerald-50 border border-emerald-100 p-5">
                <p class="text-xs font-black uppercase tracking-widest text-emerald-500"><?php echo h(localize_text('Practice Sets')); ?></p>
                <p class="text-sm text-emerald-950 mt-2"><?php echo h(localize_text('Practice tasks appear inside chapters when admin adds them.')); ?></p>
            </div>
            <div class="rounded-lg bg-amber-50 border border-amber-100 p-5">
                <p class="text-xs font-black uppercase tracking-widest text-amber-500"><?php echo h(localize_text('Downloads')); ?></p>
                <p class="text-sm text-amber-950 mt-2"><?php echo h(localize_text('Files appear in chapters when admin enables downloads; premium files require an active plan.')); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 learning-surface overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-900"><?php echo h(localize_text('Your Courses')); ?></h2>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?php echo h(localize_text('Progress Overview')); ?></span>
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
                $course_url = lang_href($next_chapter_id ? chapter_path($course['slug'], $next_chapter_id, $next_chapter_name) : course_path($course['slug']));
            ?>
                <article class="p-6 flex flex-col lg:flex-row lg:items-center gap-5">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-black text-slate-900"><?php echo h(localize_text((string)$course['title'])); ?></h3>
                        <p class="text-sm text-slate-500 mt-1 line-clamp-2"><?php echo h(localize_text((string)$course['description'])); ?></p>
                        <div class="mt-4 flex items-center gap-3">
                            <div class="flex-1 max-w-md bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $percent; ?>%"></div>
                            </div>
                            <span class="text-xs font-black text-slate-500"><?php echo $done; ?>/<?php echo $total; ?> <?php echo h(localize_text('lessons')); ?></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-black <?php echo $percent === 100 ? 'text-emerald-600' : 'text-blue-600'; ?>"><?php echo $percent; ?>%</span>
                        <a href="<?php echo h($course_url); ?>" class="edu-primary-btn py-2.5 px-5 transition"><?php echo h($done > 0 ? t('student.continue') : t('student.start')); ?></a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if(empty($courses)): ?>
                <div class="p-12 text-center text-slate-500">
                    <h3 class="text-xl font-black text-slate-900"><?php echo h(localize_text('No courses available yet')); ?></h3>
                    <p class="mt-2"><?php echo h(localize_text('New courses will appear here automatically.')); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
