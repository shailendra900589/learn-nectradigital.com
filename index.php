<?php
require_once 'includes/db.php';
secure_session_start();

// 1. Fetch Courses with chapter counts
$stmt = $pdo->query("
    SELECT c.*, COUNT(ch.id) as chapter_count 
    FROM courses c 
    LEFT JOIN chapters ch ON c.id = ch.course_id 
    GROUP BY c.id 
    ORDER BY c.created_at DESC
");
$courses = $stmt->fetchAll();

$stmtRecommended = $pdo->query("
    SELECT c.title, c.slug, c.description, COUNT(DISTINCT sp.id) AS popularity
    FROM courses c
    LEFT JOIN student_progress sp ON sp.course_id = c.id
    GROUP BY c.id
    ORDER BY popularity DESC, c.created_at DESC
    LIMIT 6
");
$recommended_courses = $stmtRecommended->fetchAll();

// 2. Homepage ad surfaces are disabled. Ads now render only inside lesson content/text.
$topAd = '';
$bottomAd = '';

// 3. Set Auto-SEO Variables for this specific page
$seo_title = "Free Coding Tutorials";
$seo_description = "Browse programming courses with clear lessons, examples, search, and progress tracking.";
$seo_keywords = "coding tutorials, php course, javascript tutorials, programming roadmap, web development courses";

// 4. Load the Reusable Header
require_once 'includes/header.php';
?>

<div class="site-hero-light pt-24 pb-40 text-center relative overflow-hidden border-b border-blue-100">
    <div class="max-w-4xl mx-auto px-4 relative z-10 flex flex-col items-center">
        <span class="edu-pill mb-6">
            Structured Developer Learning
        </span>
        <h1 class="text-5xl md:text-7xl font-black text-slate-950 mb-8 leading-[1.1] tracking-tight">
            Learn by Reading, <br>
            Practicing, and Finishing
        </h1>
        <p class="text-lg md:text-xl text-slate-600 mb-10 font-medium max-w-2xl leading-relaxed">
            Clear tutorials, progress tracking, searchable courses, and focused lessons built for learners who want reference-quality explanations without noise.
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="#courses" class="edu-primary-btn py-4 px-10 transition-all transform hover:-translate-y-1 text-lg flex items-center justify-center gap-2">
                Explore Courses
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative -mt-24 z-20">
    
    <?php if($topAd): ?>
        <div class="w-full flex justify-center mb-16">
            <div class="bg-white/90 backdrop-blur-md border border-gray-200 p-3 rounded-2xl shadow-xl text-center w-full max-w-5xl min-h-[120px] flex flex-col items-center justify-center">
                <span class="block mb-2 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Sponsored Advertisement</span>
                <div class="w-full flex justify-center items-center overflow-x-auto no-scrollbar">
                    <?php echo $topAd; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-14">
        <div class="bg-white border border-gray-200 rounded-lg p-6 learning-surface">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V6a2 2 0 012-2z"></path></svg>
            </div>
            <h3 class="font-black text-slate-900 text-lg mb-2">Reference-Style Lessons</h3>
            <p class="text-sm text-slate-500 leading-6">Courses are organized as bite-size chapters with a persistent syllabus and quick navigation.</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 learning-surface">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="font-black text-slate-900 text-lg mb-2">Progress Tracking</h3>
            <p class="text-sm text-slate-500 leading-6">Students can mark lessons complete and return exactly where they left off.</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 learning-surface">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.6-5.15a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0z"></path></svg>
            </div>
            <h3 class="font-black text-slate-900 text-lg mb-2">Fast Search</h3>
            <p class="text-sm text-slate-500 leading-6">Search courses from the header and jump straight into the right tutorial.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-14">
        <div class="bg-white border border-indigo-100 rounded-xl p-6 learning-surface">
            <p class="text-xs font-black tracking-widest uppercase text-indigo-500">Search Visibility</p>
            <p class="mt-2 text-sm text-slate-600 leading-6">Feeds are now optimized for search crawlers and recommendation engines.</p>
        </div>
        <div class="bg-white border border-emerald-100 rounded-xl p-6 learning-surface">
            <p class="text-xs font-black tracking-widest uppercase text-emerald-500">AEO/GEO Ready</p>
            <p class="mt-2 text-sm text-slate-600 leading-6">Structured metadata improves answer-engine understanding and local relevance signals.</p>
        </div>
        <div class="bg-white border border-amber-100 rounded-xl p-6 learning-surface">
            <p class="text-xs font-black tracking-widest uppercase text-amber-500">Auto Notifications</p>
            <p class="mt-2 text-sm text-slate-600 leading-6">Users receive in-app progress and recommended-learning alerts in real time.</p>
        </div>
    </div>

    <?php if(!empty($recommended_courses)): ?>
        <section class="mb-16">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Recommended For Search & Learners</h2>
                    <p class="text-slate-500 mt-2 font-medium">Trending tutorials picked from live learner activity.</p>
                </div>
                <a href="recommendations-feed.json" class="hidden md:inline-flex bg-indigo-50 text-indigo-700 border border-indigo-100 text-sm font-bold px-4 py-2 rounded-lg">Open JSON Feed</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($recommended_courses as $rec): ?>
                    <a href="<?php echo h(course_path($rec['slug'])); ?>" class="bg-white border border-slate-200 rounded-xl p-6 learning-surface hover:border-indigo-300 transition">
                        <p class="text-[10px] uppercase tracking-widest font-black text-indigo-500 mb-2">Recommendation</p>
                        <h3 class="text-xl font-black text-slate-900"><?php echo h($rec['title']); ?></h3>
                        <p class="text-sm text-slate-500 mt-3 leading-6"><?php echo h(seo_excerpt($rec['description'], 120)); ?></p>
                        <div class="mt-4 text-xs font-black text-indigo-700">Learner hits: <?php echo number_format((int)$rec['popularity']); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <div id="courses" class="mb-16 scroll-mt-32">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Available Courses</h2>
                <p class="text-gray-500 mt-2 font-medium">Select a topic to begin your journey.</p>
            </div>
            <span class="hidden md:inline-flex bg-blue-50 text-blue-700 border border-blue-100 text-sm font-bold px-4 py-2 rounded-lg shadow-sm">
                <?php echo count($courses); ?> Courses Inside
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(count($courses) > 0): ?>
                <?php foreach($courses as $course): 
                    // Format the date dynamically
                    $created_date = date('M Y', strtotime($course['created_at']));
                ?>
                    <a href="<?php echo h(course_path($course['slug'])); ?>" class="bg-white rounded-2xl shadow-lg hover:shadow-2xl hover:shadow-blue-900/5 transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col group transform hover:-translate-y-1.5 relative">
                        <div class="h-2.5 w-full bg-gradient-to-r from-blue-600 to-indigo-500"></div>
                        
                        <div class="p-8 flex-grow flex flex-col relative z-10 bg-white">
                            <div class="mb-4 flex justify-between items-start">
                                <span class="bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-md border border-blue-100">
                                    Tutorial
                                </span>
                                <span class="text-xs font-bold text-gray-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Updated <?php echo $created_date; ?>
                                </span>
                            </div>

                            <h3 class="text-2xl font-black text-gray-900 mb-3 group-hover:text-blue-600 transition-colors leading-tight">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </h3>
                            
                            <p class="text-gray-500 text-sm mb-8 flex-grow leading-relaxed line-clamp-3">
                                <?php echo htmlspecialchars($course['description'] ?: 'Master the fundamentals and advanced concepts of ' . $course['title'] . ' with this step-by-step interactive guide.'); ?>
                            </p>
                            
                            <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-100">
                                <span class="text-xs font-bold text-slate-600 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </span>
                                    <?php echo $course['chapter_count']; ?> Lessons
                                </span>
                                
                                <span class="text-blue-600 font-black text-sm flex items-center group-hover:translate-x-1 transition-transform">
                                    Start Learning 
                                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-20 bg-white rounded-3xl border border-dashed border-gray-300 shadow-sm">
                    <div class="text-5xl mb-4">📚</div>
                    <h3 class="text-2xl font-black text-gray-900">No courses available yet</h3>
                    <p class="mt-2 text-gray-500 font-medium">We are currently preparing amazing content. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($bottomAd): ?>
        <div class="w-full flex justify-center mt-16 mb-8">
            <div class="bg-white border border-gray-200 p-3 rounded-2xl shadow-sm text-center w-full min-h-[120px] flex flex-col items-center justify-center">
                <span class="block mb-2 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Sponsored Advertisement</span>
                <div class="w-full flex justify-center items-center overflow-x-auto no-scrollbar">
                    <?php echo $bottomAd; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php 
// 5. Load the Reusable Footer
require_once 'includes/footer.php'; 
?>
