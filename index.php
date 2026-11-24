<?php
require_once 'includes/db.php';
secure_session_start();

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (preg_match('#/(?:index|index\.php)/?$#i', $requestPath)) {
    header('Location: ' . localized_url(''), true, 301);
    exit;
}

// 1. Fetch Courses with chapter counts
$rankHeat = course_rank_heat_sql('r', true);
$stmt = $pdo->query("
    SELECT c.*, COUNT(ch.id) as chapter_count, {$rankHeat} AS rank_heat
    FROM courses c
    LEFT JOIN chapters ch ON c.id = ch.course_id
    LEFT JOIN course_rank r ON r.course_id = c.id
    GROUP BY c.id
    ORDER BY rank_heat DESC, c.id ASC
");
$courses = $stmt->fetchAll();

$seo_title = "Learn to Code - Complete Programming & Web Development Tutorials";
$seo_description = "Master PHP, Python, JavaScript, C, C#, HTML5, CSS3, SQL, data structures, machine learning, and AI from absolute scratch. Reference-grade documentation, live executable code playgrounds, line-by-line explanations, and self-assessment quizzes.";
if (current_lang() !== 'en') {
    $catalogBag = [$seo_title, $seo_description];
    foreach ($courses as $course) {
        $catalogBag[] = (string)$course['title'];
        $catalogBag[] = (string)$course['description'];
    }
    $catalogDone = localize_texts($catalogBag);
    $seo_title = $catalogDone[0];
    $seo_description = $catalogDone[1];
}

require_once 'includes/header.php';
?>

<section class="library-hero" aria-labelledby="library-title">
    <div class="library-shell library-intro">
        <div>
            <p class="library-kicker"><?php echo h(t('home.kicker')); ?></p>
            <h1 id="library-title"><?php echo h(t('home.title_a')); ?><br><em><?php echo h(t('home.title_b')); ?></em></h1>
            <p><?php echo h(t('home.lead')); ?></p>
            <div class="library-actions">
                <a href="<?php echo h(lang_href('index#courses')); ?>" class="edu-primary-btn"><?php echo h(t('home.explore')); ?></a>
                <a href="<?php echo h(lang_href(isset($_SESSION['student_logged_in']) ? 'student' : 'login')); ?>" class="library-secondary"><?php echo h(t('home.my')); ?></a>
            </div>
        </div>
        <div class="library-workbench">
            <div class="workbench-bar"><span><?php echo h(t('home.lab')); ?></span><span>JavaScript</span></div>
            <pre><code><span>const</span> skills = ["Read", "Practice", "Build"];
skills.forEach(skill =&gt; console.log(skill));</code></pre>
            <div class="workbench-output"><strong><?php echo h(t('home.output')); ?></strong><span>Read</span><span>Practice</span><span>Build</span></div>
            <button type="button" class="workbench-run" data-library-example><?php echo h(t('home.open_lab')); ?></button>
        </div>
    </div>
</section>

<section class="library-shell course-library" id="courses" aria-labelledby="catalog-title">
    <div class="catalog-heading">
        <div>
            <p class="library-kicker"><?php echo h(t('home.catalog_kicker')); ?></p>
            <h2 id="catalog-title"><?php echo h(t('home.catalog_title')); ?></h2>
            <p><?php echo count($courses); ?> <?php echo h(t('home.courses_word')); ?> &middot; <?php echo array_sum(array_column($courses, 'chapter_count')); ?> <?php echo h(t('home.lessons_word')); ?> &middot; <?php echo h(t('home.ranked')); ?></p>
        </div>
        <div class="catalog-filter" hidden>
            <label for="catalog-query"><?php echo h(t('home.filter')); ?></label>
            <input type="search" id="catalog-query" placeholder="<?php echo h(t('home.filter_ph')); ?>">
        </div>
    </div>
    <p class="sr-only" id="catalog-count" role="status"></p>
    <p id="catalog-empty" class="catalog-empty" <?php echo $courses ? 'hidden' : ''; ?>>
        <?php echo h($courses ? t('home.empty') : t('home.none')); ?>
    </p>
    <div class="catalog-grid" id="catalog-grid">
        <?php foreach ($courses as $course): ?>
            <a class="catalog-card" href="<?php echo h(lang_href(course_path($course['slug']))); ?>">
                <p class="catalog-meta"><?php echo (int)$course['chapter_count']; ?> <?php echo h(t('home.lessons_word')); ?></p>
                <h3><?php echo h(localize_text((string)$course['title'])); ?></h3>
                <p class="catalog-description"><?php echo h(localize_text((string)$course['description'])); ?></p>
                <span class="catalog-action"><?php echo h(t('home.view')); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
