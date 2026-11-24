<?php
require_once 'includes/db.php';
enforce_request_budget('search', 45, 60);
secure_session_start();

$query = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$courses = [];
$chapters = [];

if ($query !== '' && strlen($query) <= 80) {
    $q = like_contains($query);

    // 1. Search Chapters with parent course (by title, section, keywords, or content body)
    $stmtChap = $pdo->prepare("
        SELECT ch.id as chapter_id, ch.chapter_name, ch.section_name, ch.order_index, c.id as course_id, c.title as course_title, c.slug as course_slug,
               " . course_rank_heat_sql('r') . " AS rank_heat
        FROM chapters ch
        JOIN courses c ON ch.course_id = c.id
        LEFT JOIN course_rank r ON r.course_id = c.id
        WHERE ch.is_premium = 0 AND (ch.chapter_name LIKE :q1 ESCAPE '\\\\' OR ch.keywords LIKE :q2 ESCAPE '\\\\' OR ch.section_name LIKE :q3 ESCAPE '\\\\' OR ch.content LIKE :q4 ESCAPE '\\\\')
        ORDER BY (ch.chapter_name LIKE :qExact1) DESC, (ch.keywords LIKE :qExact2) DESC, rank_heat DESC, c.id ASC, ch.order_index ASC
        LIMIT 50
    ");
    $stmtChap->execute([
        'q1' => $q,
        'q2' => $q,
        'q3' => $q,
        'q4' => $q,
        'qExact1' => $q,
        'qExact2' => $q
    ]);
    $chapters = $stmtChap->fetchAll();

    // 2. Search Courses
    $stmtCourse = $pdo->prepare("
        SELECT c.*, COUNT(ch.id) AS chapter_count, " . course_rank_heat_sql('r', true) . " AS rank_heat
        FROM courses c
        LEFT JOIN chapters ch ON ch.course_id = c.id
        LEFT JOIN course_rank r ON r.course_id = c.id
        WHERE c.title LIKE :q1 ESCAPE '\\\\' OR c.keywords LIKE :q2 ESCAPE '\\\\' OR c.description LIKE :q3 ESCAPE '\\\\'
        GROUP BY c.id
        ORDER BY (c.title LIKE :qExact) DESC, rank_heat DESC, c.id ASC
        LIMIT 10
    ");
    $stmtCourse->execute(['q1' => $q, 'q2' => $q, 'q3' => $q, 'qExact' => $q]);
    $courses = $stmtCourse->fetchAll();

    if (course_rank_query_counts($query)) {
        $rankedIds = [];
        foreach ($courses as $course) {
            $rankedIds[] = (int)$course['id'];
        }
        foreach ($chapters as $chap) {
            $rankedIds[] = (int)$chap['course_id'];
        }
        record_course_signals($pdo, $rankedIds, 'search');
    }
}

if (current_lang() !== 'en' && ($chapters || $courses)) {
    $searchBag = [];
    foreach ($chapters as $chap) {
        $searchBag[] = (string)$chap['chapter_name'];
        $searchBag[] = (string)$chap['section_name'];
        $searchBag[] = (string)$chap['course_title'];
    }
    foreach ($courses as $course) {
        $searchBag[] = (string)$course['title'];
        $searchBag[] = (string)$course['description'];
    }
    localize_texts($searchBag);
}

$seo_title = $query ? t('search.results') . ': ' . $query : t('search.page_title');
$seo_description = $query ? t('search.page_desc') . ' ' . $query . '.' : t('search.page_desc');
$seo_canonical = site_base_url() . 'search' . ($query ? '?q=' . rawurlencode($query) : '');
require_once 'includes/header.php';
?>

<section class="search-hero py-14 border-b border-slate-800 relative overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center sm:text-left">
        <span class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-3.5 py-1 rounded-full text-xs font-bold tracking-wider uppercase mb-3 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <?php echo h(t('search.kicker')); ?>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight"><?php echo h(t('search.title')); ?></h1>
        <p class="text-slate-300 text-sm mt-1.5"><?php echo h(t('search.lead')); ?></p>
        
        <form method="GET" action="<?php echo h(lang_href('search')); ?>" class="mt-6 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="search" name="q" aria-label="<?php echo h(t('search.aria')); ?>" value="<?php echo h($query); ?>" class="w-full rounded-xl border border-slate-700 bg-white px-4 py-3.5 text-slate-900 outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-sm shadow-inner" placeholder="<?php echo h(t('search.placeholder_long')); ?>">
            </div>
            <button class="edu-primary-btn py-3.5 px-8 text-sm font-bold shadow-lg hover:shadow-emerald-500/20">
                <?php echo h(t('search.button')); ?> &raquo;
            </button>
        </form>

        <!-- Popular quick search tags -->
        <div class="flex flex-wrap items-center gap-2 mt-4 text-xs">
            <span class="text-slate-400 font-semibold"><?php echo h(t('search.popular')); ?></span>
            <?php 
            $quickTags = ['Array', 'Loops', 'Flexbox', 'Functions', 'SELECT', 'OOP', 'HTML5', 'Promises'];
            foreach($quickTags as $tag): 
            ?>
                <a href="<?php echo h(lang_href('search?q=' . urlencode($tag))); ?>" class="bg-slate-800/80 hover:bg-emerald-600 text-slate-300 hover:text-white px-2.5 py-1 rounded-md border border-slate-700 transition">
                    <?php echo h($tag); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?php if ($query): ?>
        <div class="mb-8 pb-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                <?php echo h(t('search.results')); ?> "<span class="text-emerald-600"><?php echo h($query); ?></span>"
            </h2>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1 rounded">
                <?php echo count($chapters) + count($courses); ?> <?php echo h(t('search.matches')); ?>
            </span>
        </div>

        <?php if (!empty($chapters)): ?>
            <div class="mb-10">
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-500 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <?php echo h(t('search.lessons')); ?> (<?php echo count($chapters); ?>)
                </h3>
                <div class="grid gap-3">
                    <?php foreach($chapters as $chap): 
                        $targetUrl = lang_href(course_path($chap['course_slug']) . '/' . (int)$chap['chapter_id'] . '-' . slugify($chap['chapter_name'], 'chapter'));
                    ?>
                        <a href="<?php echo h($targetUrl); ?>" class="bg-white rounded-xl border border-slate-200 hover:border-emerald-500 p-4 transition shadow-sm hover:shadow-md flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-xs flex items-center justify-center border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <?php echo (int)$chap['order_index']; ?>
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 group-hover:text-emerald-700 transition-colors text-base"><?php echo h(localize_text((string)$chap['chapter_name'])); ?></h4>
                                    <span class="text-xs text-slate-400"><?php echo h(localize_text((string)$chap['section_name'])); ?></span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded">
                                <?php echo h(localize_text((string)$chap['course_title'])); ?> &raquo;
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($courses)): ?>
            <div>
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-500 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <?php echo h(t('search.tracks')); ?> (<?php echo count($courses); ?>)
                </h3>
                <div class="grid gap-4">
                    <?php foreach($courses as $course): ?>
                        <a href="<?php echo h(lang_href(course_path($course['slug']))); ?>" class="block bg-white rounded-xl border border-slate-200 hover:border-emerald-500 p-6 shadow-sm hover:shadow-md transition group">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h4 class="text-lg font-black text-slate-900 group-hover:text-emerald-600 transition-colors"><?php echo h(localize_text((string)$course['title'])); ?></h4>
                                    <p class="text-slate-500 text-sm mt-1.5 leading-relaxed"><?php echo h(localize_text((string)$course['description'])); ?></p>
                                </div>
                                <span class="shrink-0 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-md px-3 py-1"><?php echo (int)$course['chapter_count']; ?> <?php echo h(t('home.lessons_word')); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($chapters) && empty($courses)): ?>
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-500">
                <div class="text-4xl mb-3">🔍</div>
                <h3 class="text-xl font-black text-slate-900"><?php echo h(t('search.none_title')); ?></h3>
                <p class="mt-2 text-sm text-slate-500"><?php echo h(t('search.none_body')); ?></p>
                <a href="<?php echo h(lang_href('index#courses')); ?>" class="inline-block mt-6 edu-primary-btn py-2.5 px-6 text-xs font-bold"><?php echo h(t('search.browse')); ?></a>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="text-center py-16 text-slate-500">
            <h3 class="text-xl font-bold text-slate-800"><?php echo h(t('search.start_title')); ?></h3>
            <p class="text-sm text-slate-500 mt-2"><?php echo h(t('search.start_body')); ?></p>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
