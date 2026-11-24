<?php
require_once 'includes/db.php';
enforce_request_budget('public-profile', 45, 60);
secure_session_start();

$token = is_string($_GET['token'] ?? null) ? trim($_GET['token']) : '';
$student = get_public_student_profile($pdo, $token);

if (!$student || (int)($student['profile_public'] ?? 0) !== 1 || ($student['account_status'] ?? 'active') !== 'active') {
    http_response_code(404);
    $seo_title = localize_text('Profile Not Found');
    $seo_description = localize_text('This student profile could not be found.');
    $seo_robots = 'noindex, nofollow';
    require_once 'includes/header.php';
    echo '<section class="max-w-3xl mx-auto px-4 py-20 text-center"><h1 class="text-4xl font-black text-slate-900">' . h(localize_text('Profile not found')) . '</h1><p class="mt-3 text-slate-500">' . h(localize_text('The QR profile link may be invalid or expired.')) . '</p></section>';
    require_once 'includes/footer.php';
    exit;
}

$student_id = (int)$student['id'];
$activity = student_activity_summary($pdo, $student_id);
$skills = split_skills($student['skills'] ?? '');
$photo = profile_photo_url($student['profile_photo'] ?? '');
$studentNumber = 'LN-' . str_pad((string)$student_id, 6, '0', STR_PAD_LEFT);

$profileBag = [
    'Profile Not Found',
    'This student profile could not be found.',
    'Profile not found',
    'The QR profile link may be invalid or expired.',
    'Profile',
    'Student learner',
    'Learning profile and course activity.',
    'Learn.Nectra learner profile',
    'Lessons completed',
    'Courses completed',
    'About',
    'Course progress',
    'Course progress will appear when this learner completes a lesson.',
    'Skills',
    'Learning activity',
    'Courses started',
    'Quizzes passed',
    'Quiz attempts',
    'Average quiz score',
    'Contact and links',
    'Website',
    'Skills and activity',
];
foreach (($activity['courses'] ?? []) as $course) {
    $profileBag[] = (string)$course['title'];
    $done = (int)$course['completed_lessons'];
    $total = (int)$course['total_lessons'];
    $profileBag[] = $done . ' of ' . $total . ' lessons completed';
}
if (current_lang() !== 'en') {
    localize_texts($profileBag);
}
$seo_title = $student['name'] . ' · ' . localize_text('Profile');
$seo_description = seo_excerpt(($student['headline'] ?: localize_text('Student learner')) . ' - ' . ($student['bio'] ?: localize_text('Learning profile and course activity.')), 120);
$seo_canonical = absolute_url('profile/' . $student['public_token']);
$seo_type = 'profile';
$seo_robots = 'noindex, nofollow, noarchive';
header('X-Robots-Tag: noindex, nofollow, noarchive');
$seo_schema = [
    [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $student['name'],
        'email' => ((int)($student['public_email'] ?? 0) === 1) ? $student['email'] : null,
        'jobTitle' => $student['headline'] ?: localize_text('Student learner'),
        'url' => $seo_canonical,
        'image' => $photo ? absolute_url($photo) : null,
        'sameAs' => array_values(array_filter([$student['website'] ?? '', $student['linkedin'] ?? '', $student['github'] ?? ''])),
    ],
];
$links = [];
foreach (['website' => 'Website', 'linkedin' => 'LinkedIn', 'github' => 'GitHub'] as $field => $label) {
    $url = (string)($student[$field] ?? '');
    if (filter_var($url, FILTER_VALIDATE_URL) && in_array(strtolower((string)parse_url($url, PHP_URL_SCHEME)), ['https', 'http'], true)) {
        $links[$label] = $url;
    }
}
$seo_schema[0]['sameAs'] = array_values($links);
$initial = preg_match('/^\X/u', trim($student['name']), $initialMatch) ? strtoupper($initialMatch[0]) : 'L';
$showEmail = (int)($student['public_email'] ?? 0) === 1 && !empty($student['email']);
$showPhone = (int)($student['public_phone'] ?? 0) === 1 && !empty($student['phone']);
require_once 'includes/header.php';
?>

<section class="learner-hero" aria-labelledby="learner-name">
    <div class="library-shell learner-identity">
        <div class="learner-avatar" aria-hidden="true">
            <?php if ($photo): ?>
                <img src="<?php echo h($photo); ?>" alt="" width="128" height="128">
            <?php else: ?>
                <?php echo h($initial); ?>
            <?php endif; ?>
        </div>
        <div class="learner-nameplate">
            <p class="learner-kicker"><?php echo h(localize_text('Learn.Nectra learner profile')); ?></p>
            <h1 id="learner-name"><?php echo h($student['name']); ?></h1>
            <p class="learner-headline"><?php echo h($student['headline'] ?: localize_text('Student learner')); ?></p>
            <div class="learner-meta">
                <span><?php echo h($studentNumber); ?></span>
                <?php if (!empty($student['location'])): ?><span><?php echo h($student['location']); ?></span><?php endif; ?>
            </div>
        </div>
        <dl class="learner-hero-stats">
            <div><dt><?php echo h(localize_text('Lessons completed')); ?></dt><dd><?php echo number_format((int)$activity['completed_lessons']); ?></dd></div>
            <div><dt><?php echo h(localize_text('Courses completed')); ?></dt><dd><?php echo number_format((int)$activity['completed_courses']); ?></dd></div>
        </dl>
    </div>
</section>

<div class="library-shell learner-layout">
    <div class="learner-primary">
        <?php if (!empty($student['bio'])): ?>
            <section class="learner-card">
                <h2><?php echo h(localize_text('About')); ?></h2>
                <p class="learner-bio"><?php echo h($student['bio']); ?></p>
            </section>
        <?php endif; ?>

        <section class="learner-card" aria-labelledby="learner-progress-title">
            <h2 id="learner-progress-title"><?php echo h(localize_text('Course progress')); ?></h2>
            <?php if ($activity['courses']): ?>
                <ul class="learner-progress-list">
                    <?php foreach ($activity['courses'] as $course):
                        $total = (int)$course['total_lessons'];
                        $done = (int)$course['completed_lessons'];
                        $percent = $total > 0 ? min(100, (int)round($done / $total * 100)) : 0;
                    ?>
                        <li>
                            <div class="learner-progress-title">
                                <h3><?php echo h(localize_text((string)$course['title'])); ?></h3><span><?php echo $percent; ?>%</span>
                            </div>
                            <progress max="100" value="<?php echo $percent; ?>" aria-label="<?php echo h(localize_text((string)$course['title'])); ?>"></progress>
                            <p><?php echo h(localize_text($done . ' of ' . $total . ' lessons completed')); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="learner-muted"><?php echo h(localize_text('Course progress will appear when this learner completes a lesson.')); ?></p>
            <?php endif; ?>
        </section>
    </div>

    <aside class="learner-secondary" aria-label="<?php echo h(localize_text('Skills and activity')); ?>">
        <?php if ($skills): ?>
            <section class="learner-card">
                <h2><?php echo h(localize_text('Skills')); ?></h2>
                <ul class="learner-skills">
                    <?php foreach ($skills as $skill): ?><li><?php echo h($skill); ?></li><?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
        <section class="learner-card">
            <h2><?php echo h(localize_text('Learning activity')); ?></h2>
            <dl class="learner-activity">
                <div><dt><?php echo h(localize_text('Courses started')); ?></dt><dd><?php echo number_format((int)$activity['started_courses']); ?></dd></div>
                <div><dt><?php echo h(localize_text('Quizzes passed')); ?></dt><dd><?php echo number_format((int)$activity['quizzes_passed']); ?></dd></div>
                <div><dt><?php echo h(localize_text('Quiz attempts')); ?></dt><dd><?php echo number_format((int)$activity['quiz_attempts']); ?></dd></div>
                <?php if ((int)$activity['quiz_attempts'] > 0): ?>
                    <div><dt><?php echo h(localize_text('Average quiz score')); ?></dt><dd><?php echo h((string)$activity['average_quiz_score']); ?>%</dd></div>
                <?php endif; ?>
            </dl>
        </section>
        <?php if ($showEmail || $showPhone || $links): ?>
            <section class="learner-card">
                <h2><?php echo h(localize_text('Contact and links')); ?></h2>
                <ul class="learner-contact">
                    <?php if ($showEmail): ?><li><a href="mailto:<?php echo h($student['email']); ?>"><?php echo h($student['email']); ?></a></li><?php endif; ?>
                    <?php if ($showPhone): ?><li><?php echo h($student['phone']); ?></li><?php endif; ?>
                    <?php foreach ($links as $label => $url): ?><li><a href="<?php echo h($url); ?>" rel="noopener noreferrer" target="_blank"><?php echo h($label === 'Website' ? localize_text('Website') : $label); ?></a></li><?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
    </aside>
</div>

<?php require_once 'includes/footer.php'; ?>
