<?php
require_once 'includes/db.php';
secure_session_start();

$query = trim($_GET['q'] ?? '');
$results = [];
if ($query !== '' && strlen($query) <= 80) {
    $stmt = $pdo->prepare("
        SELECT c.*, COUNT(ch.id) AS chapter_count
        FROM courses c
        LEFT JOIN chapters ch ON ch.course_id = c.id
        WHERE c.title LIKE :title_q OR c.description LIKE :description_q
        GROUP BY c.id
        ORDER BY c.created_at DESC
        LIMIT 30
    ");
    $q = '%' . $query . '%';
    $stmt->execute(['title_q' => $q, 'description_q' => $q]);
    $results = $stmt->fetchAll();
}

$seo_title = $query ? 'Search: ' . $query : 'Search Tutorials';
$seo_description = $query ? 'Find programming tutorials and courses related to ' . $query . '.' : 'Search programming tutorials, lessons, and courses.';
$seo_canonical = site_base_url() . 'search' . ($query ? '?q=' . rawurlencode($query) : '');
$seo_keywords = $query ? ($query . ', coding search, programming course search') : 'search tutorials, coding topics, learning search';
$seo_schema = [
    [
        '@context' => 'https://schema.org',
        '@type' => 'SearchResultsPage',
        'name' => $seo_title,
        'description' => $seo_description,
        'url' => $seo_canonical,
    ],
];
require_once 'includes/header.php';
?>

<section class="bg-slate-950 editorial-grid py-12 border-b border-slate-800">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-black text-white">Search Tutorials</h1>
        <form method="GET" action="search" class="mt-6 flex gap-3">
            <input type="search" name="q" value="<?php echo h($query); ?>" class="flex-1 rounded-lg border border-slate-700 bg-white px-4 py-3 text-slate-900 outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search SEO, PHP, JavaScript...">
            <button class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 rounded-lg">Search</button>
        </form>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <h2 class="text-2xl font-black text-slate-900"><?php echo $query ? count($results) . ' results for "' . h($query) . '"' : 'Enter a topic to search'; ?></h2>
    </div>

    <div class="grid gap-5">
        <?php foreach($results as $course): ?>
            <a href="<?php echo h(course_path($course['slug'])); ?>" class="block bg-white rounded-lg border border-gray-200 p-6 learning-surface hover:border-blue-300 transition">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-black text-slate-900"><?php echo h($course['title']); ?></h3>
                        <p class="text-slate-500 mt-2 leading-6"><?php echo h($course['description']); ?></p>
                    </div>
                    <span class="shrink-0 text-xs font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-md px-3 py-1"><?php echo (int)$course['chapter_count']; ?> lessons</span>
                </div>
            </a>
        <?php endforeach; ?>
        <?php if($query && empty($results)): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-10 text-center text-slate-500">
                <h3 class="text-xl font-black text-slate-900">No matching tutorials found</h3>
                <p class="mt-2">Try a shorter topic or browse all courses.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
