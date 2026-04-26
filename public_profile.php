<?php
require_once 'includes/db.php';
secure_session_start();

$token = trim($_GET['token'] ?? '');
$student = get_public_student_profile($pdo, $token);

if (!$student || (int)($student['profile_public'] ?? 1) !== 1 || ($student['account_status'] ?? 'active') !== 'active') {
    http_response_code(404);
    $seo_title = 'Profile Not Found';
    $seo_description = 'This student profile could not be found.';
    $seo_robots = 'noindex, nofollow';
    require_once 'includes/header.php';
    echo '<section class="max-w-3xl mx-auto px-4 py-20 text-center"><h1 class="text-4xl font-black text-slate-900">Profile not found</h1><p class="mt-3 text-slate-500">The QR profile link may be invalid or expired.</p></section>';
    require_once 'includes/footer.php';
    exit;
}

$student_id = (int)$student['id'];
$activity = student_activity_summary($pdo, $student_id);
$skills = split_skills($student['skills'] ?? '');
$photo = profile_photo_url($student['profile_photo'] ?? '');
$studentNumber = 'LN-' . str_pad((string)$student_id, 6, '0', STR_PAD_LEFT);

$seo_title = $student['name'] . ' Profile';
$seo_description = seo_excerpt(($student['headline'] ?: 'Student learner') . ' - ' . ($student['bio'] ?: 'Learning profile and course activity.'), 120);
$seo_canonical = absolute_url('profile/' . $student['public_token']);
$seo_type = 'profile';
$seo_schema = [
    [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $student['name'],
        'email' => ((int)($student['public_email'] ?? 0) === 1) ? $student['email'] : null,
        'jobTitle' => $student['headline'] ?: 'Student Learner',
        'url' => $seo_canonical,
        'image' => $photo ? absolute_url($photo) : null,
        'sameAs' => array_values(array_filter([$student['website'] ?? '', $student['linkedin'] ?? '', $student['github'] ?? ''])),
    ],
];
require_once 'includes/header.php';
?>

<section class="site-hero-light py-14 border-b border-blue-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-8 items-center">
            <div class="w-44 h-44 rounded-2xl overflow-hidden bg-white border border-blue-100 shadow-2xl">
                <?php if($photo): ?>
                    <img src="<?php echo h($photo); ?>" alt="<?php echo h($student['name']); ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-6xl font-black text-white bg-blue-600"><?php echo h(strtoupper(substr($student['name'], 0, 1))); ?></div>
                <?php endif; ?>
            </div>
            <div>
                <span class="edu-pill mb-4">Verified Learning Profile</span>
                <h1 class="text-4xl md:text-6xl font-black text-slate-950 tracking-tight"><?php echo h($student['name']); ?></h1>
                <p class="text-xl text-blue-700 font-bold mt-3"><?php echo h($student['headline'] ?: 'Student Learner'); ?></p>
                <div class="mt-5 flex flex-wrap gap-3 text-sm text-slate-600">
                    <span class="bg-white border border-blue-100 rounded-lg px-3 py-2"><?php echo h($studentNumber); ?></span>
                    <?php if(!empty($student['location'])): ?><span class="bg-white border border-blue-100 rounded-lg px-3 py-2"><?php echo h($student['location']); ?></span><?php endif; ?>
                    <?php if((int)($student['public_email'] ?? 0) === 1): ?><span class="bg-white border border-blue-100 rounded-lg px-3 py-2"><?php echo h($student['email']); ?></span><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <main class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-900 mb-4">Professional Summary</h2>
                <p class="text-slate-600 leading-8"><?php echo h($student['bio'] ?: 'This learner is building skills through Learn.Nectra courses and practical lessons.'); ?></p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6 md:p-8">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-black text-slate-900">Learning Activity</h2>
                    <span class="text-xs font-black uppercase tracking-widest text-slate-400">Auto Updated</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="rounded-lg bg-blue-50 border border-blue-100 p-5">
                        <div class="text-3xl font-black text-blue-700"><?php echo (int)$activity['completed_lessons']; ?></div>
                        <div class="text-xs font-black uppercase tracking-widest text-blue-500 mt-1">Lessons Completed</div>
                    </div>
                    <div class="rounded-lg bg-emerald-50 border border-emerald-100 p-5">
                        <div class="text-3xl font-black text-emerald-700"><?php echo (int)$activity['started_courses']; ?></div>
                        <div class="text-xs font-black uppercase tracking-widest text-emerald-500 mt-1">Courses Started</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 border border-slate-200 p-5">
                        <div class="text-3xl font-black text-slate-900"><?php echo (int)$activity['completed_courses']; ?></div>
                        <div class="text-xs font-black uppercase tracking-widest text-slate-500 mt-1">Courses Completed</div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div class="rounded-lg bg-amber-50 border border-amber-100 p-5">
                        <div class="text-3xl font-black text-amber-700"><?php echo h((string)$activity['average_quiz_score']); ?>%</div>
                        <div class="text-xs font-black uppercase tracking-widest text-amber-500 mt-1">Average Quiz Score</div>
                    </div>
                    <div class="rounded-lg bg-violet-50 border border-violet-100 p-5">
                        <div class="text-3xl font-black text-violet-700"><?php echo (int)$activity['quizzes_passed']; ?></div>
                        <div class="text-xs font-black uppercase tracking-widest text-violet-500 mt-1">Quizzes Passed</div>
                    </div>
                    <div class="rounded-lg bg-cyan-50 border border-cyan-100 p-5">
                        <div class="text-3xl font-black text-cyan-700"><?php echo (int)$activity['quiz_attempts']; ?></div>
                        <div class="text-xs font-black uppercase tracking-widest text-cyan-500 mt-1">Quiz Attempts</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-900 mb-5">Course Progress</h2>
                <div class="space-y-4">
                    <?php foreach($activity['courses'] as $course):
                        $total = (int)$course['total_lessons'];
                        $done = (int)$course['completed_lessons'];
                        $percent = $total > 0 ? round(($done / $total) * 100) : 0;
                    ?>
                        <div class="border border-gray-100 rounded-lg p-4">
                            <div class="flex items-center justify-between gap-4 mb-2">
                                <h3 class="font-black text-slate-900"><?php echo h($course['title']); ?></h3>
                                <span class="text-sm font-black text-blue-600"><?php echo $percent; ?>%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $percent; ?>%"></div>
                            </div>
                            <p class="text-xs font-bold text-slate-500 mt-2"><?php echo $done; ?> of <?php echo $total; ?> lessons completed</p>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($activity['courses'])): ?>
                        <p class="text-slate-500">No public course activity yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <aside class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                <h2 class="text-xl font-black text-slate-900 mb-4">Skills</h2>
                <div class="flex flex-wrap gap-2">
                    <?php foreach($skills as $skill): ?>
                        <span class="bg-blue-50 text-blue-700 border border-blue-100 rounded-full px-3 py-1 text-sm font-bold"><?php echo h($skill); ?></span>
                    <?php endforeach; ?>
                    <?php if(empty($skills)): ?>
                        <span class="text-slate-500 text-sm">Skills will appear after the student updates their profile.</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 learning-surface p-6">
                <h2 class="text-xl font-black text-slate-900 mb-4">Contact & Links</h2>
                <div class="space-y-3 text-sm">
                    <?php if((int)($student['public_email'] ?? 0) === 1): ?><a href="mailto:<?php echo h($student['email']); ?>" class="block text-blue-600 font-bold break-all"><?php echo h($student['email']); ?></a><?php endif; ?>
                    <?php if((int)($student['public_phone'] ?? 0) === 1 && !empty($student['phone'])): ?><div class="text-slate-600 font-bold"><?php echo h($student['phone']); ?></div><?php endif; ?>
                    <?php foreach(['website' => 'Website', 'linkedin' => 'LinkedIn', 'github' => 'GitHub'] as $field => $label): ?>
                        <?php if(!empty($student[$field])): ?>
                            <a href="<?php echo h($student[$field]); ?>" target="_blank" rel="noopener" class="block text-blue-600 font-bold"><?php echo h($label); ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
