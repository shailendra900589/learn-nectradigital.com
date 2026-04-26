<?php
require_once 'includes/db.php';
secure_session_start();

// --- 1. HANDLE AJAX "MARK AS COMPLETE" REQUEST ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'mark_complete') {
    verify_csrf();
    if (!isset($_SESSION['student_logged_in'])) {
        json_response(['status' => 'error', 'message' => 'Please log in to track progress.'], 401);
    }
    
    $chapter_id = post_int('chapter_id');
    $course_id = post_int('course_id');
    $student_id = $_SESSION['student_id'];
    
    try {
        $stmtCheck = $pdo->prepare("SELECT id, quiz_json, require_quiz_pass FROM chapters WHERE id = ? AND course_id = ?");
        $stmtCheck->execute([$chapter_id, $course_id]);
        $chapterCheck = $stmtCheck->fetch();
        if (!$chapterCheck) {
            json_response(['status' => 'error', 'message' => 'Invalid chapter selection.'], 400);
        }
        $quiz = json_decode((string)($chapterCheck['quiz_json'] ?? ''), true);
        if ((int)($chapterCheck['require_quiz_pass'] ?? 1) === 1 && is_array($quiz) && count($quiz) > 0) {
            $stmtPassed = $pdo->prepare("SELECT id FROM student_quiz_attempts WHERE student_id = ? AND chapter_id = ? AND passed = 1 LIMIT 1");
            $stmtPassed->execute([$student_id, $chapter_id]);
            if (!$stmtPassed->fetch()) {
                json_response(['status' => 'error', 'message' => 'Pass the chapter quiz before marking this lesson complete.'], 403);
            }
        }

        $stmtBest = $pdo->prepare("SELECT MAX(score) FROM student_quiz_attempts WHERE student_id = ? AND chapter_id = ?");
        $stmtBest->execute([$student_id, $chapter_id]);
        $bestScore = $stmtBest->fetchColumn();

        $stmt = $pdo->prepare("
            INSERT INTO student_progress (student_id, chapter_id, course_id, quiz_score, quiz_passed)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE quiz_score = VALUES(quiz_score), quiz_passed = VALUES(quiz_passed)
        ");
        $stmt->execute([$student_id, $chapter_id, $course_id, $bestScore !== false ? $bestScore : null, $bestScore !== false && (float)$bestScore >= 70 ? 1 : 0]);
        json_response(['status' => 'success']);
    } catch(PDOException $e) {
        json_response(['status' => 'error', 'message' => 'Database error.'], 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_quiz') {
    verify_csrf();
    if (!isset($_SESSION['student_logged_in'])) {
        json_response(['status' => 'error', 'message' => 'Please log in to submit quizzes.'], 401);
    }

    $chapter_id = post_int('chapter_id');
    $course_id = post_int('course_id');
    $student_id = (int)$_SESSION['student_id'];

    $stmtChapter = $pdo->prepare("SELECT quiz_json FROM chapters WHERE id = :id AND course_id = :course_id LIMIT 1");
    $stmtChapter->execute(['id' => $chapter_id, 'course_id' => $course_id]);
    $quiz = json_decode((string)$stmtChapter->fetchColumn(), true);
    if (!is_array($quiz) || count($quiz) === 0) {
        json_response(['status' => 'error', 'message' => 'No quiz is available for this chapter.'], 400);
    }

    $answers = $_POST['answers'] ?? [];
    $requestedIndices = array_filter(array_map('trim', explode(',', (string)($_POST['quiz_indices'] ?? ''))), fn($value) => $value !== '');
    $questionIndices = [];
    foreach ($requestedIndices as $index) {
        if (ctype_digit($index)) {
            $intIndex = (int)$index;
            if (isset($quiz[$intIndex]) && !in_array($intIndex, $questionIndices, true)) {
                $questionIndices[] = $intIndex;
            }
        }
    }
    if (!$questionIndices) {
        $questionIndices = array_keys($quiz);
    }
    $questionIndices = array_slice($questionIndices, 0, 4);

    $correct = 0;
    $total = count($questionIndices);
    foreach ($questionIndices as $index) {
        $item = $quiz[$index];
        $submitted = trim((string)($answers[$index] ?? ''));
        if (hash_equals((string)($item['answer'] ?? ''), $submitted)) {
            $correct++;
        }
    }
    $score = $total > 0 ? round(($correct / $total) * 100, 2) : 0;
    $passed = $score >= 70 ? 1 : 0;

    $stmtAttempt = $pdo->prepare("
        INSERT INTO student_quiz_attempts (student_id, course_id, chapter_id, score, correct_count, total_questions, answers_json, passed)
        VALUES (:student_id, :course_id, :chapter_id, :score, :correct_count, :total_questions, :answers_json, :passed)
    ");
    $stmtAttempt->execute([
        'student_id' => $student_id,
        'course_id' => $course_id,
        'chapter_id' => $chapter_id,
        'score' => $score,
        'correct_count' => $correct,
        'total_questions' => $total,
        'answers_json' => json_encode(['indices' => $questionIndices, 'answers' => $answers]),
        'passed' => $passed,
    ]);

    if ($passed) {
        $stmtProgress = $pdo->prepare("
            INSERT INTO student_progress (student_id, chapter_id, course_id, quiz_score, quiz_passed)
            VALUES (:student_id, :chapter_id, :course_id, :score, 1)
            ON DUPLICATE KEY UPDATE quiz_score = GREATEST(COALESCE(quiz_score, 0), VALUES(quiz_score)), quiz_passed = 1
        ");
        $stmtProgress->execute(['student_id' => $student_id, 'chapter_id' => $chapter_id, 'course_id' => $course_id, 'score' => $score]);
    }

    json_response([
        'status' => 'success',
        'score' => $score,
        'correct' => $correct,
        'total' => $total,
        'passed' => (bool)$passed,
        'message' => $passed ? 'Quiz passed. Lesson completion is unlocked.' : 'Score at least 70% to pass this quiz.',
    ]);
}

// --- 2. HANDLE AJAX REQUEST FOR CHAPTER CONTENT ---
if (isset($_GET['ajax_chapter_id'])) {
    $chapter_id = get_int('ajax_chapter_id');
    $course_id = get_int('course_id');
    
    $stmt = $pdo->prepare("SELECT * FROM chapters WHERE id = :id AND course_id = :course_id");
    $stmt->execute(['id' => $chapter_id, 'course_id' => $course_id]);
    $chapter = $stmt->fetch();
    
    if ($chapter) {
        if (isset($_SESSION['student_logged_in']) && !student_can_view_content($pdo, (int)$_SESSION['student_id'])) {
            http_response_code(403);
            echo '<div class="p-8 bg-amber-50 text-amber-800 rounded-xl border border-amber-100 font-medium">Your course access is currently paused. Please contact support or check your billing page.</div>';
            exit;
        }
        $activeSubscription = isset($_SESSION['student_logged_in']) ? active_subscription($pdo, (int)$_SESSION['student_id']) : null;
        if ((int)($chapter['is_premium'] ?? 0) === 1 && !$activeSubscription) {
            echo '<div class="p-8 bg-blue-50 text-blue-900 rounded-xl border border-blue-100 font-medium"><h2 class="text-2xl font-black mb-2">Premium chapter</h2><p>This lesson is available for premium students. Upgrade your plan to unlock premium lessons, downloads, and practice reports.</p><a href="billing" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-lg">View Plans</a></div>';
            exit;
        }

        $content = render_chapter_content($chapter['content']);
        $is_completed = false;
        
        if (isset($_SESSION['student_logged_in'])) {
            $stmtProg = $pdo->prepare("SELECT id FROM student_progress WHERE student_id = ? AND chapter_id = ?");
            $stmtProg->execute([$_SESSION['student_id'], $chapter_id]);
            if ($stmtProg->fetch()) $is_completed = true;
        }
        
        // Inject content-only ads if active. Side/top/bottom ad surfaces are intentionally disabled.
        $contentAds = [];
        if (should_show_ads($pdo)) {
            $locations = array_keys(content_ad_locations());
            $placeholders = implode(',', array_fill(0, count($locations), '?'));
            $stmtAds = $pdo->prepare("SELECT location, ad_code FROM ads WHERE location IN ($placeholders) AND is_active = 1 ORDER BY RAND()");
            $stmtAds->execute($locations);
            foreach ($stmtAds->fetchAll() as $ad) {
                $contentAds[$ad['location']][] = $ad['ad_code'];
            }
        }

        $renderAdGroup = function (array $adCodes, string $label): string {
            $adCodes = array_values(array_filter($adCodes, fn($code) => trim((string)$code) !== ''));
            if (!$adCodes) {
                return '';
            }

            $html = '';
            foreach (array_slice($adCodes, 0, 3) as $adCode) {
                $html .= render_ad_slot($adCode, $label);
            }
            return $html;
        };

        $insertAfterParagraph = function (string $html, array $adCodes, int $paragraphNumber, string $label) use ($renderAdGroup): string {
            $adHtml = $renderAdGroup($adCodes, $label);
            if ($adHtml === '') {
                return $html;
            }
            $count = 0;
            $inserted = false;
            $updated = preg_replace_callback('/<\/p>/i', function ($match) use (&$count, &$inserted, $paragraphNumber, $adHtml) {
                $count++;
                if ($count === $paragraphNumber) {
                    $inserted = true;
                    return $match[0] . "\n" . $adHtml;
                }
                return $match[0];
            }, $html, $paragraphNumber + 2) ?? $html;
            return $inserted ? $updated : $updated . $adHtml;
        };

        $labels = content_ad_locations();
        $content = $insertAfterParagraph($content, $contentAds['inline_after_first'] ?? [], 1, $labels['inline_after_first']);
        $content = $insertAfterParagraph($content, $contentAds['text_link'] ?? [], 2, $labels['text_link']);
        $content = $insertAfterParagraph($content, $contentAds['sponsored_note'] ?? [], 3, $labels['sponsored_note']);
        $content = $insertAfterParagraph($content, $contentAds['resource_box'] ?? [], 4, $labels['resource_box']);
        $content = $insertAfterParagraph($content, $contentAds['inline_mid'] ?? [], 5, $labels['inline_mid']);
        $content = $insertAfterParagraph($content, $contentAds['cta_card'] ?? [], 6, $labels['cta_card']);
        $content = $insertAfterParagraph($content, $contentAds['code_break'] ?? [], 7, $labels['code_break']);
        $content = $insertAfterParagraph($content, $contentAds['quiz_prompt'] ?? [], 8, $labels['quiz_prompt']);

        if (!empty($contentAds['inline_before_summary'])) {
            $content .= $renderAdGroup($contentAds['inline_before_summary'], $labels['inline_before_summary']);
        }
        if (!empty($contentAds['bottom_recommendation'])) {
            $content .= $renderAdGroup($contentAds['bottom_recommendation'], $labels['bottom_recommendation']);
        }

        echo '<h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-8 leading-tight">' . h($chapter['chapter_name']) . '</h2>';
        // max-w-none ensures the content stretches full width when ads are disabled
        echo '<div class="prose prose-lg prose-blue max-w-none w-full overflow-hidden prose-pre:bg-slate-900 prose-pre:text-slate-100 prose-headings:font-bold prose-a:text-blue-600 prose-img:rounded-xl prose-img:shadow-md">' . $content . '</div>';

        if (!empty($chapter['practice_content'])) {
            echo '<section class="mt-10 rounded-2xl border border-emerald-100 bg-emerald-50 p-6">';
            echo '<h3 class="text-2xl font-black text-emerald-950 mb-3">Practice Set</h3>';
            echo '<div class="prose prose-emerald max-w-none">' . render_chapter_content($chapter['practice_content']) . '</div>';
            echo '</section>';
        }

        $downloadUrl = chapter_download_url($chapter['download_file'] ?? '');
        if ($downloadUrl !== '') {
            $downloadAllowed = !(int)($chapter['is_premium'] ?? 0) || $activeSubscription;
            echo '<section class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">';
            echo '<div><h3 class="text-xl font-black text-slate-900">Chapter Download</h3><p class="text-sm text-slate-500 mt-1">' . h($chapter['download_label'] ?: 'Download learning file') . '</p></div>';
            if ($downloadAllowed) {
                echo '<a href="' . h($downloadUrl) . '" download class="edu-primary-btn px-5 py-3 text-center">Download File</a>';
            } else {
                echo '<a href="billing" class="edu-primary-btn px-5 py-3 text-center">Unlock Download</a>';
            }
            echo '</section>';
        }

        $quiz = json_decode((string)($chapter['quiz_json'] ?? ''), true);
        $quizPassed = false;
        $bestScore = null;
        if (isset($_SESSION['student_logged_in']) && is_array($quiz) && count($quiz) > 0) {
            $stmtBestQuiz = $pdo->prepare("SELECT MAX(score) AS best_score, MAX(passed) AS passed FROM student_quiz_attempts WHERE student_id = ? AND chapter_id = ?");
            $stmtBestQuiz->execute([(int)$_SESSION['student_id'], (int)$chapter['id']]);
            $bestQuiz = $stmtBestQuiz->fetch() ?: [];
            $bestScore = $bestQuiz['best_score'] ?? null;
            $quizPassed = (int)($bestQuiz['passed'] ?? 0) === 1;
        }

        if (is_array($quiz) && count($quiz) > 0) {
            echo '<section class="mt-10 rounded-2xl border border-blue-100 bg-blue-50 p-6" id="chapter-quiz">';
            echo '<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5"><div><h3 class="text-2xl font-black text-blue-950">Chapter Quiz</h3><p class="text-sm text-blue-800 mt-1">Score 70% or higher to pass.</p></div>';
            if ($bestScore !== null) {
                echo '<span class="text-sm font-black ' . ($quizPassed ? 'text-emerald-700' : 'text-amber-700') . '">Best score: ' . h((string)round((float)$bestScore, 2)) . '%</span>';
            }
            echo '</div>';
            if (!isset($_SESSION['student_logged_in'])) {
                echo '<p class="rounded-lg bg-white border border-blue-100 p-4 text-sm text-slate-600"><a href="login" class="text-blue-600 font-bold">Log in</a> to submit the quiz and save your report.</p>';
            } else {
                $quizIndices = array_keys($quiz);
                shuffle($quizIndices);
                $quizIndices = array_slice($quizIndices, 0, min(4, count($quizIndices)));
                echo '<form id="quiz-form-' . (int)$chapter['id'] . '" class="space-y-5">';
                echo '<input type="hidden" name="action" value="submit_quiz"><input type="hidden" name="chapter_id" value="' . (int)$chapter['id'] . '"><input type="hidden" name="course_id" value="' . (int)$course_id . '"><input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
                echo '<input type="hidden" name="quiz_indices" value="' . h(implode(',', $quizIndices)) . '">';
                foreach ($quizIndices as $displayNumber => $index) {
                    $item = $quiz[$index];
                    $options = (array)($item['options'] ?? []);
                    shuffle($options);
                    echo '<fieldset class="rounded-xl bg-white border border-blue-100 p-4"><legend class="font-black text-slate-900 mb-3">' . h(($displayNumber + 1) . '. ' . ($item['question'] ?? 'Question')) . '</legend>';
                    foreach ($options as $option) {
                        echo '<label class="flex items-center gap-3 py-2 text-sm font-bold text-slate-700"><input type="radio" name="answers[' . (int)$index . ']" value="' . h((string)$option) . '" required class="w-4 h-4 text-blue-600"> ' . h((string)$option) . '</label>';
                    }
                    echo '</fieldset>';
                }
                echo '<div id="quiz-result-' . (int)$chapter['id'] . '" class="hidden rounded-lg p-4 text-sm font-bold"></div>';
                echo '<button type="button" onclick="submitChapterQuiz(' . (int)$chapter['id'] . ')" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3 rounded-lg">Submit Quiz</button>';
                echo '</form>';
            }
            echo '</section>';
        }
        
        echo '<div class="mt-14 pt-8 border-t border-gray-100 flex items-center justify-between">';
        if (!isset($_SESSION['student_logged_in'])) {
            echo '<p class="text-sm text-gray-500 bg-gray-50 px-5 py-4 rounded-xl border border-gray-200 w-full"><a href="login" class="text-blue-600 font-bold hover:underline">Log in</a> or register to track your learning progress and save your spot.</p>';
        } else {
            if ($is_completed) {
                echo '<button disabled class="flex items-center gap-2 bg-green-50 text-green-700 font-bold py-3.5 px-8 rounded-xl cursor-default border border-green-200 shadow-sm"><svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Completed</button>';
            } elseif (is_array($quiz ?? null) && count($quiz ?? []) > 0 && (int)($chapter['require_quiz_pass'] ?? 1) === 1 && !$quizPassed) {
                echo '<button disabled class="flex items-center gap-2 bg-amber-50 text-amber-700 font-bold py-3.5 px-8 rounded-xl cursor-default border border-amber-200 shadow-sm">Pass Quiz to Complete</button>';
            } else {
                echo '<button onclick="markChapterComplete('.$chapter_id.', '.$course_id.')" id="btn-complete-'.$chapter_id.'" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-xl transition shadow-lg hover:shadow-blue-500/30 transform hover:-translate-y-0.5"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Mark as Complete</button>';
            }
        }
        echo '</div>';
    } else {
        echo '<div class="p-8 bg-red-50 text-red-600 rounded-xl border border-red-100 font-medium">Chapter content could not be loaded. It may have been deleted.</div>';
    }
    exit; 
}

// --- 3. NORMAL PAGE LOAD START ---
if (!isset($_GET['slug'])) {
    header('Location: ' . app_path('index'));
    exit;
}

$slug = slugify((string)$_GET['slug'], '');

$stmtCourse = $pdo->prepare("SELECT * FROM courses WHERE slug = :slug");
$stmtCourse->execute(['slug' => $slug]);
$course = $stmtCourse->fetch();

if (!$course) {
    header('Location: ' . app_path('index'));
    exit;
}

$stmtChapters = $pdo->prepare("
    SELECT MIN(id) AS id, chapter_name, order_index
    FROM chapters
    WHERE course_id = :course_id
    GROUP BY order_index, chapter_name
    ORDER BY order_index ASC, id ASC
");
$stmtChapters->execute(['course_id' => $course['id']]);
$chapters = $stmtChapters->fetchAll();
$total_chapters = count($chapters);

$chapter_ids = array_map('intval', array_column($chapters, 'id'));
$requested_chapter_id = isset($_GET['chapter_id']) ? (int)$_GET['chapter_id'] : 0;
$active_chapter_id = in_array($requested_chapter_id, $chapter_ids, true) ? $requested_chapter_id : ($chapters[0]['id'] ?? 0);
$active_chapter = null;
if ($active_chapter_id) {
    $stmtActive = $pdo->prepare("SELECT chapter_name, content FROM chapters WHERE id = :id AND course_id = :course_id");
    $stmtActive->execute(['id' => $active_chapter_id, 'course_id' => $course['id']]);
    $active_chapter = $stmtActive->fetch();
}

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && !isset($_GET['ajax_chapter_id'])
    && isset($_GET['slug'])
    && str_contains($_SERVER['REQUEST_URI'] ?? '', 'course?slug=')
) {
    $cleanPath = $active_chapter ? chapter_path($course['slug'], $active_chapter_id, $active_chapter['chapter_name']) : course_path($course['slug']);
    header('Location: ' . absolute_url($cleanPath), true, 301);
    exit;
}

$completed_chapters = [];
$progress_percentage = 0;
if (isset($_SESSION['student_logged_in'])) {
    if (!student_can_view_content($pdo, (int)$_SESSION['student_id'])) {
        $active_chapter = null;
    }
    $stmtProg = $pdo->prepare("SELECT chapter_id FROM student_progress WHERE student_id = ? AND course_id = ?");
    $stmtProg->execute([$_SESSION['student_id'], $course['id']]);
    $completed_chapters = $stmtProg->fetchAll(PDO::FETCH_COLUMN);
    
    if ($total_chapters > 0) {
        $progress_percentage = round((count($completed_chapters) / $total_chapters) * 100);
    }
}

$stmtRelated = $pdo->prepare("
    SELECT id, title, slug, description
    FROM courses
    WHERE id != :id
    ORDER BY created_at DESC
    LIMIT 4
");
$stmtRelated->execute(['id' => $course['id']]);
$related_courses = $stmtRelated->fetchAll();

$seo_title = $active_chapter ? $active_chapter['chapter_name'] : $course['title'] . ' Tutorial';
$seo_description = $active_chapter ? seo_excerpt($active_chapter['content'], 120) : seo_excerpt($course['description'], 120);
$seo_canonical = absolute_url($active_chapter ? chapter_path($course['slug'], $active_chapter_id, $active_chapter['chapter_name']) : course_path($course['slug']));
$seo_type = 'article';
$seo_schema = [
    [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $course['title'],
        'description' => plain_text($course['description']),
        'provider' => [
            '@type' => 'Organization',
            'name' => 'Learn.Nectra',
            'url' => site_base_url(),
        ],
        'hasCourseInstance' => [
            '@type' => 'CourseInstance',
            'courseMode' => 'online',
            'courseWorkload' => 'Self paced',
        ],
    ],
];

if ($active_chapter) {
    $seo_schema[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $active_chapter['chapter_name'],
        'description' => seo_excerpt($active_chapter['content']),
        'articleSection' => $course['title'],
        'datePublished' => date('c', strtotime($course['created_at'])),
        'dateModified' => date('c'),
        'author' => ['@type' => 'Organization', 'name' => 'Learn.Nectra'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Learn.Nectra'],
        'mainEntityOfPage' => $seo_canonical,
    ];
}

// Dynamic Stats for "Pro" feel
$enrolled_count = 1200 + ($course['id'] * 45); 
$last_updated = date('F Y', strtotime($course['created_at']));

require_once 'includes/header.php';
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />

<div class="site-hero-light pt-10 pb-14 border-b border-blue-100 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <nav class="flex text-sm text-slate-500 font-medium mb-6">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="index" class="hover:text-blue-700 transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="index#courses" class="ml-1 hover:text-blue-700 transition">Courses</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-slate-700"><?php echo h($course['title']); ?></span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex-1">
                <h1 class="text-4xl md:text-5xl font-black text-slate-950 tracking-tight mb-4"><?php echo h($course['title']); ?></h1>
                <p class="text-lg text-slate-600 max-w-3xl leading-relaxed mb-6"><?php echo h($course['description']); ?></p>
                
                <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-slate-600">
                    <span class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-md border border-blue-100 shadow-sm">
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        Beginner Friendly
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <?php echo number_format($enrolled_count); ?> Enrolled
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Last updated <?php echo $last_updated; ?>
                    </span>
                </div>
            </div>

            <div class="flex-shrink-0">
                <button onclick="copyToClipboard()" id="shareBtn" class="flex items-center gap-2 bg-white hover:bg-blue-50 text-blue-700 font-bold py-2.5 px-5 rounded-lg border border-blue-100 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    <span>Share Course</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <div class="lg:col-span-3">
            <button onclick="document.getElementById('chapter-sidebar').classList.toggle('hidden')" class="lg:hidden w-full mb-6 bg-white border border-gray-200 text-gray-800 font-bold py-3.5 px-5 rounded-xl shadow-sm flex justify-between items-center transition hover:bg-gray-50">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    Course Syllabus
                </span>
                <span class="text-gray-400 text-sm">Tap to expand v</span>
            </button>

            <aside id="chapter-sidebar" class="hidden lg:block mb-8 lg:mb-0 lg:sticky lg:top-24 lg:max-h-[calc(100vh-8rem)]">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col w-full max-h-[calc(100vh-8rem)]">
                    
                    <?php if(isset($_SESSION['student_logged_in'])): ?>
                        <div class="bg-blue-50/50 px-5 py-5 border-b border-blue-100 flex-shrink-0">
                            <div class="flex justify-between items-end mb-3">
                                <div>
                                    <span class="text-[10px] font-black text-blue-800 uppercase tracking-widest block mb-1">Your Progress</span>
                                    <span class="text-2xl font-black text-blue-600 leading-none" id="progress-text"><?php echo $progress_percentage; ?>%</span>
                                </div>
                                <span class="text-xs font-bold text-blue-600/70"><?php echo count($completed_chapters); ?>/<?php echo $total_chapters; ?></span>
                            </div>
                            <div class="w-full bg-blue-200/50 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-700 ease-out" id="progress-bar" style="width: <?php echo $progress_percentage; ?>%"></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-slate-50 px-5 py-4 border-b border-gray-200 flex-shrink-0">
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Course Syllabus</h3>
                        </div>
                    <?php endif; ?>

                    <ul class="divide-y divide-gray-100 overflow-y-auto flex-1 no-scrollbar w-full" id="chapter-list">
                        <?php if($total_chapters > 0): ?>
                            <?php foreach ($chapters as $index => $chap): 
                                $isActive = ($chap['id'] == $active_chapter_id);
                                $isDone = in_array($chap['id'], $completed_chapters);
                            ?>
                                <li class="w-full">
                                    <button 
                                        onclick="loadChapter(<?php echo $chap['id']; ?>, this); if(window.innerWidth < 1024) document.getElementById('chapter-sidebar').classList.add('hidden');" 
                                        data-id="<?php echo $chap['id']; ?>"
                                        class="w-full text-left px-4 py-3 text-sm transition-all flex gap-3 chapter-btn <?php echo $isActive ? 'bg-blue-50/50 border-l-4 border-blue-600 text-blue-800 font-bold shadow-inner' : 'border-l-4 border-transparent text-gray-600 hover:bg-slate-50 hover:text-blue-600'; ?>">
                                        
                                        <span id="badge-<?php echo $chap['id']; ?>" class="flex-shrink-0 rounded-full flex items-center justify-center text-xs font-bold chapter-number transition-colors <?php echo $isDone ? 'bg-green-500 text-white border-transparent' : ($isActive ? 'bg-blue-600 text-white border-transparent' : 'bg-slate-100 text-slate-400 border border-slate-200'); ?>">
                                            <?php if($isDone): ?>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                            <?php else: ?>
                                                <?php echo $chap['order_index']; ?>
                                            <?php endif; ?>
                                        </span>
                                        <span class="block flex-1 chapter-title"><?php echo h($chap['chapter_name']); ?></span>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="p-6 text-center text-gray-500 text-sm">No syllabus available yet.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>
        </div>

        <main class="lg:col-span-9 transition-all duration-300 w-full min-w-0">
            <div class="bg-white p-4 sm:p-6 md:p-10 rounded-xl md:rounded-2xl shadow-sm border border-gray-200 min-h-[600px] w-full overflow-hidden" id="tutorial-content">
                <div id="loading-spinner" class="hidden flex justify-center items-center py-32">
                    <svg class="animate-spin h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
                
                <div id="dynamic-area" class="w-full">
                    <?php if($total_chapters > 0): ?>
                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                loadChapter(<?php echo $active_chapter_id; ?>, document.querySelector('button[data-id="<?php echo $active_chapter_id; ?>"]'), true);
                            });
                        </script>
                    <?php else: ?>
                        <div class="text-center py-20">
                            <div class="text-6xl mb-6">🚧</div>
                            <h2 class="text-3xl font-black text-gray-800">Under Construction</h2>
                            <p class="text-lg text-gray-500 mt-2">The chapters for this course are currently being authored.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if($total_chapters > 0): ?>
                <div class="flex flex-col sm:flex-row justify-between mt-8 gap-4 w-full">
                    <button id="btn-prev" onclick="navigateChapter('prev')" class="hidden w-full sm:w-auto bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-bold py-3.5 px-8 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                        &larr; Previous Lesson
                    </button>
                    <button id="btn-next" onclick="navigateChapter('next')" class="hidden w-full sm:w-auto edu-primary-btn py-3.5 px-8 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 sm:ml-auto">
                        Next Lesson &rarr;
                    </button>
                </div>
            <?php endif; ?>

            <?php if(!empty($related_courses)): ?>
                <section class="mt-12">
                    <div class="flex items-end justify-between mb-4">
                        <h2 class="text-2xl font-black text-slate-900">Recommended Tutorials</h2>
                        <a href="index#courses" class="text-sm font-bold text-blue-600 hover:underline">View all</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach($related_courses as $related): ?>
                            <a href="<?php echo h(course_path($related['slug'])); ?>" class="bg-white border border-gray-200 rounded-lg p-5 learning-surface hover:border-blue-300 transition">
                                <h3 class="font-black text-slate-900"><?php echo h($related['title']); ?></h3>
                                <p class="text-sm text-slate-500 mt-2 line-clamp-2"><?php echo h(seo_excerpt($related['description'], 110)); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </main>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup.min.js"></script>

<script>
    function copyToClipboard() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            const btn = document.getElementById('shareBtn');
            const originalContent = btn.innerHTML;
            btn.innerHTML = `<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>Copied!</span>`;
            btn.classList.add('border-green-500', 'text-green-400');
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.classList.remove('border-green-500', 'text-green-400');
            }, 2000);
        });
    }

    const chapters = <?php echo json_encode($chapter_ids); ?>;
    let currentChapterId = <?php echo $active_chapter_id; ?>;
    const courseSlug = <?php echo json_encode($slug); ?>;
    const courseId = <?php echo $course['id']; ?>;
    const csrfToken = <?php echo json_encode(csrf_token()); ?>;
    const coursePath = <?php echo json_encode(course_path($course['slug'])); ?>;
    const chapterSlugs = <?php echo json_encode(array_column(array_map(fn($chapter) => ['id' => (int)$chapter['id'], 'slug' => slugify($chapter['chapter_name'], 'chapter')], $chapters), 'slug', 'id')); ?>;
    let completedCount = <?php echo count($completed_chapters); ?>;
    const totalChapters = <?php echo $total_chapters; ?>;

    function loadChapter(chapterId, buttonElement, isInitialLoad = false) {
        if (!chapterId || chapters.length === 0) return;
        currentChapterId = chapterId;

        document.querySelectorAll('.chapter-btn').forEach(btn => {
            btn.classList.remove('bg-blue-50/50', 'border-blue-600', 'text-blue-800', 'font-bold', 'shadow-inner');
            btn.classList.add('border-transparent', 'text-gray-600');
            
            let numBadge = btn.querySelector('.chapter-number');
            if (numBadge && !numBadge.classList.contains('bg-green-500')) {
                numBadge.classList.remove('bg-blue-600', 'text-white', 'border-transparent');
                numBadge.classList.add('bg-slate-100', 'text-slate-400', 'border', 'border-slate-200');
            }
        });

        if (buttonElement) {
            buttonElement.classList.add('bg-blue-50/50', 'border-blue-600', 'text-blue-800', 'font-bold', 'shadow-inner');
            buttonElement.classList.remove('border-transparent', 'text-gray-600');
            
            let numBadge = buttonElement.querySelector('.chapter-number');
            if (numBadge && !numBadge.classList.contains('bg-green-500')) {
                numBadge.classList.add('bg-blue-600', 'text-white', 'border-transparent');
                numBadge.classList.remove('bg-slate-100', 'text-slate-400', 'border', 'border-slate-200');
            }
        }

        const dynamicArea = document.getElementById('dynamic-area');
        const loader = document.getElementById('loading-spinner');
        
        dynamicArea.classList.add('hidden');
        loader.classList.remove('hidden');

        fetch(`course?slug=${courseSlug}&ajax_chapter_id=${chapterId}&course_id=${courseId}`)
            .then(response => response.text())
            .then(html => {
                dynamicArea.innerHTML = html;
                loader.classList.add('hidden');
                dynamicArea.classList.remove('hidden');
                
                if(window.Prism) Prism.highlightAll();

                if (!isInitialLoad) {
                    window.history.pushState({chapterId: chapterId}, '', `${coursePath}/${chapterId}-${chapterSlugs[chapterId] || 'chapter'}`);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                updateNavButtons();
            });
    }

    function markChapterComplete(chapterId, courseId) {
        const btn = document.getElementById(`btn-complete-${chapterId}`);
        btn.innerHTML = 'Saving...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('action', 'mark_complete');
        formData.append('chapter_id', chapterId);
        formData.append('course_id', courseId);
        formData.append('csrf_token', csrfToken);

        fetch(`course?slug=${courseSlug}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                btn.className = "flex items-center gap-2 bg-green-50 text-green-700 font-bold py-3.5 px-8 rounded-xl cursor-default border border-green-200 shadow-sm";
                btn.innerHTML = '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Completed';
                
                const badge = document.getElementById(`badge-${chapterId}`);
                if (badge) {
                    badge.className = "flex-shrink-0 rounded-full flex items-center justify-center text-xs font-bold chapter-number bg-green-500 text-white transition-colors border-transparent";
                    badge.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>';
                }

                completedCount++;
                let newPercentage = Math.round((completedCount / totalChapters) * 100);
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');
                
                if(progressBar && progressText) {
                    progressBar.style.width = newPercentage + '%';
                    progressText.innerText = newPercentage + '%';
                }
            } else {
                alert(data.message);
                btn.innerHTML = 'Mark as Complete';
                btn.disabled = false;
            }
        });
    }

    function submitChapterQuiz(chapterId) {
        const form = document.getElementById(`quiz-form-${chapterId}`);
        const result = document.getElementById(`quiz-result-${chapterId}`);
        if (!form || !result) return;

        const formData = new FormData(form);
        fetch(`course?slug=${courseSlug}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            result.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'border-red-100', 'bg-green-50', 'text-green-700', 'border-green-100', 'bg-amber-50', 'text-amber-700', 'border-amber-100');
            result.classList.add('border');
            if (data.status === 'success') {
                result.classList.add(data.passed ? 'bg-green-50' : 'bg-amber-50', data.passed ? 'text-green-700' : 'text-amber-700', data.passed ? 'border-green-100' : 'border-amber-100');
                result.innerText = `${data.correct}/${data.total} correct - ${data.score}% - ${data.message}`;
                if (data.passed) {
                    setTimeout(() => loadChapter(chapterId, document.querySelector(`button[data-id="${chapterId}"]`), true), 900);
                } else {
                    setTimeout(() => loadChapter(chapterId, document.querySelector(`button[data-id="${chapterId}"]`), true), 1400);
                }
            } else {
                result.classList.add('bg-red-50', 'text-red-700', 'border-red-100');
                result.innerText = data.message || 'Quiz could not be submitted.';
            }
        })
        .catch(() => {
            result.classList.remove('hidden');
            result.className = 'rounded-lg p-4 text-sm font-bold border bg-red-50 text-red-700 border-red-100';
            result.innerText = 'Quiz could not be submitted.';
        });
    }

    function updateNavButtons() {
        if(chapters.length === 0) return;
        const currentIndex = chapters.indexOf(currentChapterId);
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');

        btnPrev.classList.toggle('hidden', currentIndex <= 0);
        btnNext.classList.toggle('hidden', currentIndex >= chapters.length - 1);
    }

    function navigateChapter(direction) {
        if(chapters.length === 0) return;
        const currentIndex = chapters.indexOf(currentChapterId);
        let nextId = null;

        if (direction === 'prev' && currentIndex > 0) nextId = chapters[currentIndex - 1];
        else if (direction === 'next' && currentIndex < chapters.length - 1) nextId = chapters[currentIndex + 1];

        if (nextId) {
            if(window.innerWidth < 1024) document.getElementById('tutorial-content').scrollIntoView({ behavior: 'smooth' });
            loadChapter(nextId, document.querySelector(`button[data-id="${nextId}"]`));
        }
    }

    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.chapterId) {
            loadChapter(event.state.chapterId, document.querySelector(`button[data-id="${event.state.chapterId}"]`), true);
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
