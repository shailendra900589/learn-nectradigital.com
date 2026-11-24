<?php
require_once 'includes/db.php';
secure_session_start();

function require_lesson_access(PDO $pdo, int $studentId, bool $premium, int $courseId): void
{
    if (!student_can_view_content($pdo, $studentId) || ($premium && !student_course_access($pdo, $studentId, $courseId))) {
        json_response(['status' => 'error', 'message' => 'Your account does not have access to this lesson.'], 403);
    }
}

function lesson_quiz_signature(int $chapterId, string $indices): string
{
    return hash_hmac('sha256', 'lesson-quiz|' . $chapterId . '|' . $indices . '|' . session_id(), asset_signing_secret());
}

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
        $stmtCheck = $pdo->prepare("SELECT id, quiz_json, require_quiz_pass, is_premium FROM chapters WHERE id = ? AND course_id = ?");
        $stmtCheck->execute([$chapter_id, $course_id]);
        $chapterCheck = $stmtCheck->fetch();
        if (!$chapterCheck) {
            json_response(['status' => 'error', 'message' => 'Invalid chapter selection.'], 400);
        }
        require_lesson_access($pdo, (int)$student_id, (bool)$chapterCheck['is_premium'], (int)$course_id);
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

    enforce_request_budget('quiz-submit', 45, 60);
    $stmtChapter = $pdo->prepare("SELECT quiz_json, is_premium FROM chapters WHERE id = :id AND course_id = :course_id LIMIT 1");
    $stmtChapter->execute(['id' => $chapter_id, 'course_id' => $course_id]);
    $quizChapter = $stmtChapter->fetch();
    if (!$quizChapter) {
        json_response(['status' => 'error', 'message' => 'Invalid chapter selection.'], 400);
    }
    require_lesson_access($pdo, $student_id, (bool)$quizChapter['is_premium'], $course_id);
    $quiz = json_decode((string)$quizChapter['quiz_json'], true);
    if (!is_array($quiz) || !$quiz) {
        json_response(['status' => 'error', 'message' => 'No quiz is available for this chapter.'], 400);
    }

    $indices = $_POST['quiz_indices'] ?? null;
    $signature = $_POST['quiz_signature'] ?? null;
    $answers = $_POST['answers'] ?? null;
    if (!is_string($indices) || !is_string($signature) || !is_array($answers)
        || !preg_match('/^\d{1,3}(?:,\d{1,3}){0,3}$/D', $indices)
        || !hash_equals(lesson_quiz_signature($chapter_id, $indices), $signature)) {
        json_response(['status' => 'error', 'message' => 'Reload the lesson before submitting this quiz.'], 400);
    }
    $questionIndices = array_map('intval', explode(',', $indices));
    $submittedAnswers = [];
    foreach ($questionIndices as $index) {
        $value = $answers[$index] ?? null;
        if (!isset($quiz[$index]) || !is_string($value) || !in_array($value, $quiz[$index]['options'] ?? [], true)) {
            json_response(['status' => 'error', 'message' => 'Choose an answer for each quiz question.'], 400);
        }
        $submittedAnswers[$index] = $value;
    }

    $correct = 0;
    $total = count($questionIndices);
    foreach ($questionIndices as $index) {
        $item = $quiz[$index];
        $submitted = trim($submittedAnswers[$index]);
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
        'answers_json' => json_encode(['indices' => $questionIndices, 'answers' => $submittedAnswers]),
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
    enforce_request_budget('lesson-read', 150, 60);
    $chapter_id = get_int('ajax_chapter_id');
    $course_id = get_int('course_id');
    
    $stmt = $pdo->prepare("SELECT * FROM chapters WHERE id = :id AND course_id = :course_id");
    $stmt->execute(['id' => $chapter_id, 'course_id' => $course_id]);
    $chapter = $stmt->fetch();
    
    if ($chapter) {
        if (isset($_SESSION['student_logged_in']) && !student_can_view_content($pdo, (int)$_SESSION['student_id'])) {
            http_response_code(403);
            echo '<div class="p-8 bg-amber-50 text-amber-800 rounded-xl border border-amber-100 font-medium">' . h(t('course.paused')) . '</div>';
            exit;
        }
        ads_course_context((int)$course_id);
        $activeSubscription = isset($_SESSION['student_logged_in']) && student_course_access($pdo, (int)$_SESSION['student_id'], (int)$course_id);
        if ((int)($chapter['is_premium'] ?? 0) === 1 && !$activeSubscription) {
            echo '<div class="p-8 bg-blue-50 text-blue-900 rounded-xl border border-blue-100 font-medium"><h2 class="text-2xl font-black mb-2">' . h(t('course.premium_title')) . '</h2><p>' . h(t('course.premium_body')) . '</p><a href="' . h(lang_href('billing')) . '" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-lg">' . h(t('course.view_plans')) . '</a></div>';
            exit;
        }

        $content = render_chapter_content($chapter['content']);
        $quiz = json_decode((string)($chapter['quiz_json'] ?? ''), true);
        $lessonBag = [
            (string)$chapter['chapter_name'],
            'Lesson',
            'Practice Exercises',
            'Knowledge Check',
            'Chapter Quiz & Self-Assessment',
            'Test your understanding. Score 70% or higher to unlock lesson completion.',
            'Best Score:',
            'Passed',
            'Question',
        ];
        if (!empty($chapter['download_label'])) {
            $lessonBag[] = (string)$chapter['download_label'];
        }
        if (is_array($quiz)) {
            foreach ($quiz as $item) {
                if (!is_array($item)) {
                    continue;
                }
                if (!empty($item['question'])) {
                    $lessonBag[] = (string)$item['question'];
                }
                foreach ((array)($item['options'] ?? []) as $option) {
                    $lessonBag[] = (string)$option;
                }
            }
        }
        if (current_lang() !== 'en') {
            localize_texts($lessonBag);
        }
        $content = localize_lesson_html($content);
        $is_completed = false;
        
        if (isset($_SESSION['student_logged_in'])) {
            $stmtProg = $pdo->prepare("SELECT id FROM student_progress WHERE student_id = ? AND chapter_id = ?");
            $stmtProg->execute([$_SESSION['student_id'], $chapter_id]);
            if ($stmtProg->fetch()) $is_completed = true;
        }
        
        $content = insert_lesson_ads($content, active_content_ad_groups($pdo));

        echo '<p class="lesson-status">' . h(localize_text('Lesson')) . ' ' . (int)$chapter['order_index'] . '</p>';

        echo '<h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-6 leading-tight">' . h(localize_text((string)$chapter['chapter_name'])) . '</h2>';
        // max-w-none ensures the content stretches full width when ads are disabled
        echo '<div class="prose prose-lg max-w-none w-full overflow-hidden">' . $content . '</div>';

        if (!empty($chapter['practice_content'])) {
            echo '<section class="mt-10 rounded-xl border border-emerald-200 bg-emerald-50/60 p-6">';
            echo '<h3 class="text-xl font-black text-emerald-950 mb-3">' . h(localize_text('Practice Exercises')) . '</h3>';
            echo '<div class="prose max-w-none">' . localize_lesson_html(render_chapter_content($chapter['practice_content'])) . '</div>';
            echo '</section>';
        }

        $downloadUrl = chapter_download_url($chapter['download_file'] ?? '');
        if ($downloadUrl !== '') {
            $downloadAllowed = !(int)($chapter['is_premium'] ?? 0) || $activeSubscription;
            echo '<section class="mt-8 rounded-xl border border-slate-200 bg-slate-50 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">';
            $downloadLabel = trim((string)($chapter['download_label'] ?? ''));
            echo '<div><h3 class="text-xl font-black text-slate-900">' . h(t('course.download_title')) . '</h3><p class="text-sm text-slate-500 mt-1">' . h($downloadLabel !== '' ? localize_text($downloadLabel) : t('course.download_file')) . '</p></div>';
            if ($downloadAllowed) {
                echo '<a href="' . h($downloadUrl) . '" download class="edu-primary-btn px-5 py-2.5 text-center">' . h(t('course.download')) . '</a>';
            } else {
                echo '<a href="' . h(lang_href('billing')) . '" class="edu-primary-btn px-5 py-2.5 text-center">' . h(t('course.unlock')) . '</a>';
            }
            echo '</section>';
        }

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
            echo '<section class="mt-10 rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm" id="chapter-quiz">';
            
            if (!isset($_SESSION['student_logged_in'])) {
                // Exclusive Lock Card for Non-Logged In Visitors
                echo '<div class="text-center py-4 px-2">';
                echo '<div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mb-4 shadow-sm">';
                echo '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>';
                echo '</div>';
                echo '<span class="inline-block text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full mb-2">' . h(t('course.quiz_kicker')) . '</span>';
                echo '<h3 class="text-2xl font-black text-slate-900 mb-2">' . h(t('course.quiz_title')) . '</h3>';
                echo '<p class="text-sm text-slate-600 max-w-lg mx-auto mb-6 leading-relaxed">' . h(t('course.quiz_body')) . '</p>';
                echo '<div class="inline-flex flex-wrap items-center justify-center gap-3">';
                echo '<a href="' . h(lang_href('login')) . '" class="edu-primary-btn py-3 px-8 text-sm font-bold shadow-md">' . h(t('course.quiz_login')) . '</a>';
                echo '<a href="' . h(lang_href('login?tab=register')) . '" class="bg-white border border-slate-300 hover:border-emerald-500 text-slate-700 hover:text-emerald-700 py-3 px-7 rounded-xl text-sm font-bold shadow-sm transition">' . h(t('course.register')) . '</a>';
                echo '</div>';
                echo '<div class="mt-6 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-center gap-6 text-xs font-semibold text-slate-500">';
                echo '<span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> ' . h(t('course.pool')) . '</span>';
                echo '<span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> ' . h(t('course.badges')) . '</span>';
                echo '<span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> ' . h(t('course.certs')) . '</span>';
                echo '</div>';
                echo '</div>';
            } else {
                // Interactive Dynamic Quiz for Authenticated Students
                echo '<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 pb-4 border-b border-slate-100">';
                echo '<div>';
                echo '<span class="text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded">' . h(localize_text('Knowledge Check')) . '</span>';
                echo '<h3 class="text-xl font-black text-slate-900 mt-2">' . h(localize_text('Chapter Quiz & Self-Assessment')) . '</h3>';
                echo '<p class="text-xs text-slate-500 mt-0.5">' . h(localize_text('Test your understanding. Score 70% or higher to unlock lesson completion.')) . '</p>';
                echo '</div>';
                if ($bestScore !== null) {
                    echo '<span class="text-xs font-black px-3.5 py-1.5 rounded-full border ' . ($quizPassed ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200') . '">' . h(localize_text('Best Score:')) . ' ' . h((string)round((float)$bestScore, 2)) . '% ' . ($quizPassed ? '✓ ' . h(localize_text('Passed')) : '') . '</span>';
                }
                echo '</div>';

                // Fresh randomized question slice on each refresh
                $quizIndices = array_keys($quiz);
                shuffle($quizIndices);
                $quizIndices = array_slice($quizIndices, 0, min(4, count($quizIndices)));

                echo '<form id="quiz-form-' . (int)$chapter['id'] . '" class="space-y-5">';
                echo '<input type="hidden" name="action" value="submit_quiz">';
                echo '<input type="hidden" name="chapter_id" value="' . (int)$chapter['id'] . '">';
                echo '<input type="hidden" name="course_id" value="' . (int)$course_id . '">';
                echo '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
                echo '<input type="hidden" name="quiz_indices" value="' . h(implode(',', $quizIndices)) . '">';
                echo '<input type="hidden" name="quiz_signature" value="' . h(lesson_quiz_signature((int)$chapter['id'], implode(',', $quizIndices))) . '">';

                foreach ($quizIndices as $displayNumber => $index) {
                    $item = $quiz[$index];
                    $options = (array)($item['options'] ?? []);
                    shuffle($options);

                    echo '<fieldset class="rounded-xl bg-slate-50/70 border border-slate-200/80 p-5 transition hover:border-slate-300">';
                    echo '<legend class="font-bold text-slate-900 text-sm mb-3 px-2 bg-white rounded border border-slate-200 py-0.5 shadow-2xs">' . h(($displayNumber + 1) . '. ' . localize_text((string)($item['question'] ?? 'Question'))) . '</legend>';
                    echo '<div class="space-y-2 mt-1">';
                    foreach ($options as $option) {
                        echo '<label class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm font-medium text-slate-700 cursor-pointer hover:bg-emerald-50/60 hover:text-emerald-900 transition-colors border border-transparent hover:border-emerald-200">';
                        echo '<input type="radio" name="answers[' . (int)$index . ']" value="' . h((string)$option) . '" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300"> ';
                        echo '<span>' . h(localize_text((string)$option)) . '</span>';
                        echo '</label>';
                    }
                    echo '</div>';
                    echo '</fieldset>';
                }

                echo '<div id="quiz-result-' . (int)$chapter['id'] . '" class="hidden rounded-xl p-4 text-sm font-bold transition"></div>';
                echo '<div class="flex items-center gap-4 pt-2">';
                echo '<button type="button" onclick="submitChapterQuiz(' . (int)$chapter['id'] . ')" class="edu-primary-btn py-3 px-8 font-bold text-sm shadow-md flex items-center gap-2">';
                echo '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                echo h(t('course.submit_quiz'));
                echo '</button>';
                echo '<span class="text-xs text-slate-400">' . h(t('course.quiz_note')) . '</span>';
                echo '</div>';
                echo '</form>';
            }
            echo '</section>';
        }
        
        echo '<div class="mt-10 pt-6 border-t border-slate-200 flex items-center justify-between">';
        if (!isset($_SESSION['student_logged_in'])) {
            echo '<p class="text-sm text-slate-500 bg-slate-50 px-5 py-4 rounded-xl border border-slate-200 w-full"><a href="' . h(lang_href('login')) . '" class="text-emerald-600 font-bold hover:underline">' . h(t('course.login')) . '</a> ' . h(t('course.save_rest')) . '</p>';
        } else {
            if ($is_completed) {
                echo '<button disabled class="flex items-center gap-2 bg-emerald-50 text-emerald-700 font-bold py-3 px-6 rounded-lg cursor-default border border-emerald-200 shadow-sm text-sm"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ' . h(t('course.completed')) . '</button>';
            } elseif (is_array($quiz ?? null) && count($quiz ?? []) > 0 && (int)($chapter['require_quiz_pass'] ?? 1) === 1 && !$quizPassed) {
                echo '<button disabled class="flex items-center gap-2 bg-amber-50 text-amber-700 font-bold py-3 px-6 rounded-lg cursor-default border border-amber-200 shadow-sm text-sm">' . h(t('course.pass_quiz')) . '</button>';
            } else {
                echo '<button onclick="markChapterComplete('.$chapter_id.', '.$course_id.')" id="btn-complete-'.$chapter_id.'" class="edu-primary-btn py-3 px-7 text-sm font-bold shadow-md"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> ' . h(t('course.mark')) . '</button>';
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
    header('Location: ' . localized_url(''));
    exit;
}

$slug = slugify((string)$_GET['slug'], '');

$stmtCourse = $pdo->prepare("SELECT * FROM courses WHERE slug = :slug");
$stmtCourse->execute(['slug' => $slug]);
$course = $stmtCourse->fetch();

if (!$course) {
    header('Location: ' . localized_url(''));
    exit;
}

$stmtChapters = $pdo->prepare("
    SELECT MIN(id) AS id, chapter_name, COALESCE(section_name, 'Tutorial') AS section_name, order_index
    FROM chapters
    WHERE course_id = :course_id
    GROUP BY order_index, chapter_name, section_name
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
    $stmtActive = $pdo->prepare("SELECT chapter_name, CASE WHEN is_premium = 0 THEN content ELSE NULL END AS content, keywords, section_name FROM chapters WHERE id = :id AND course_id = :course_id");
    $stmtActive->execute(['id' => $active_chapter_id, 'course_id' => $course['id']]);
    $active_chapter = $stmtActive->fetch();
}

if (
    in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)
    && !isset($_GET['ajax_chapter_id'])
    && $active_chapter
) {
    $canonicalPath = chapter_path($course['slug'], $active_chapter_id, (string)$active_chapter['chapter_name']);
    $expectedPath = parse_url(localized_url($canonicalPath), PHP_URL_PATH) ?: '';
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $normalizePath = static function (string $path): string {
        $path = rawurldecode($path);
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    };
    if ($normalizePath($requestPath) !== $normalizePath($expectedPath)) {
        header('Location: ' . localized_url($canonicalPath), true, 301);
        exit;
    }
}

ads_course_context((int)$course['id']);
$packageState = 'guest';
if (isset($_SESSION['student_logged_in'])) {
    $packageStudentId = (int)$_SESSION['student_id'];
    if (student_course_access($pdo, $packageStudentId, (int)$course['id'])) {
        $packageState = 'owned';
    } elseif (student_free_course_slots($pdo, $packageStudentId) > 0) {
        $packageState = 'slot';
    } else {
        $packageState = 'buy';
    }
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

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    record_course_signal($pdo, (int)$course['id'], 'open');
    if (course_rank_referer_is_reach()) {
        record_course_signal($pdo, (int)$course['id'], 'reach');
    }
}

$relatedHeat = course_rank_heat_sql('r');
$stmtRelated = $pdo->prepare("
    SELECT c.id, c.title, c.slug, c.description
    FROM courses c
    LEFT JOIN course_rank r ON r.course_id = c.id
    WHERE c.id != :id
    ORDER BY {$relatedHeat} DESC, c.id ASC
    LIMIT 4
");
$stmtRelated->execute(['id' => $course['id']]);
$related_courses = $stmtRelated->fetchAll();

$courseTitleEn = (string)$course['title'];
$courseDescriptionEn = (string)$course['description'];
$chapterNameEn = $active_chapter ? (string)$active_chapter['chapter_name'] : '';
$seoTitleEn = $active_chapter
    ? lesson_seo_title($chapterNameEn, $courseTitleEn)
    : $courseTitleEn;
$seoDescriptionEn = $active_chapter
    ? 'Learn ' . $chapterNameEn . ' in this comprehensive ' . $courseTitleEn . ' masterclass. ' . seo_excerpt(plain_text((string)$active_chapter['content']), 110)
    : 'Complete ' . $courseTitleEn . ' developer course from basics to advanced. ' . seo_excerpt($courseDescriptionEn, 110);
$translationBag = [
    $courseTitleEn,
    $courseDescriptionEn,
    $seoTitleEn,
    $seoDescriptionEn,
    'Self paced',
    'Try it Yourself',
    'Copy Code',
    'Copy Tree',
    'Copy Command',
    'Copied!',
    'Explore Structure',
    'Run in Terminal',
    'Campaign studio',
    'Digital Marketing',
    'Milk List, one shared household list, Rs 499 a year. Each lesson is a screen from that campaign.',
    'lessons',
    'Rs 499',
    'the offer',
    'next step',
    'Audience',
    'Page',
    'Search',
    'Content',
    'Email',
    'Ads',
    'Measure',
    'Search studio',
    'SEO',
    'Milk List, one shared household list. Each lesson is a query, the page that answers it, or the report that shows what happened.',
    'query',
    'page',
    'Query',
    'Index',
    'Links',
    'Trust',
    'Excel studio',
    'Excel',
    "Asha's April household spend. Each lesson is one sheet decision: a cell, a formula, a chart, a pivot, or a check you can explain.",
    'workbook',
    'April',
    'the month',
    'Grid',
    'Formula',
    'Lookup',
    'Chart',
    'Pivot',
    'Ad-free in your package',
    'Add to my package',
    'Go ad-free from Rs 199',
];
foreach ($chapters as $chap) {
    $translationBag[] = (string)$chap['chapter_name'];
    $translationBag[] = (string)($chap['section_name'] ?? 'Tutorial');
}
foreach ($related_courses as $related) {
    $translationBag[] = (string)$related['title'];
    $translationBag[] = seo_excerpt((string)$related['description'], 110);
}
if (current_lang() !== 'en') {
    localize_texts($translationBag);
}
$courseTitle = localize_text($courseTitleEn);
$courseUrl = localized_url(course_path($course['slug']));
$seo_title = localize_text($seoTitleEn);
$seo_description = localize_text($seoDescriptionEn);
$seo_keywords = $active_chapter && !empty($active_chapter['keywords']) ? $active_chapter['keywords'] : ($course['keywords'] ?? '');
$seo_canonical = $active_chapter
    ? localized_url(chapter_path($course['slug'], $active_chapter_id, $chapterNameEn))
    : $courseUrl;

$seo_type = 'article';

// Rich Schema.org markup: Course + Article + BreadcrumbList
$breadcrumbItems = [
    [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => t('course.home'),
        'item' => localized_url(''),
    ],
    [
        '@type' => 'ListItem',
        'position' => 2,
        'name' => $courseTitle,
        'item' => $courseUrl,
    ]
];

if ($active_chapter) {
    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => 3,
        'name' => localize_text($chapterNameEn),
        'item' => $seo_canonical,
    ];
}

$seo_schema = [
    [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $courseTitle,
        'description' => plain_text(localize_text($courseDescriptionEn)),
        'inLanguage' => supported_languages()[current_lang()]['html'],
        'keywords' => $course['keywords'] ?? $seo_keywords,
        'provider' => [
            '@type' => 'Organization',
            'name' => 'Learn.Nectra',
            'url' => site_base_url(),
        ],
        'hasCourseInstance' => [
            '@type' => 'CourseInstance',
            'courseMode' => 'online',
            'courseWorkload' => localize_text('Self paced'),
        ],
    ],
    [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems,
    ]
];

if ($active_chapter) {
    $seo_schema[] = [
        '@context' => 'https://schema.org',
        '@type' => 'TechArticle',
        'headline' => localize_text($chapterNameEn),
        'description' => $seo_description,
        'keywords' => $seo_keywords,
        'articleSection' => $courseTitle,
        'datePublished' => date('c', !empty($course['created_at']) ? strtotime($course['created_at']) : time()),
        'dateModified' => date('c'),
        'author' => [
            '@type' => 'Organization',
            'name' => 'Learn.Nectra',
            'url' => site_base_url(),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'Learn.Nectra',
            'url' => site_base_url(),
        ],
        'mainEntityOfPage' => $seo_canonical,
    ];
}

// Dynamic Stats for "Pro" feel
$enrolled_count = 1200 + ($course['id'] * 45); 
$last_updated = date('F Y', strtotime($course['created_at']));

$completedLookup = array_flip(array_map('intval', $completed_chapters));
$syllabusSections = [];
$sectionIndexMap = [];
$syllabusItems = [];

foreach ($chapters as $chap) {
    $secName = (string)($chap['section_name'] ?? 'Tutorial');
    if (!array_key_exists($secName, $sectionIndexMap)) {
        $sectionIndexMap[$secName] = count($syllabusSections);
        $syllabusSections[] = localize_text($secName);
    }
    $cid = (int)$chap['id'];
    $syllabusItems[] = [
        $cid,
        (int)$chap['order_index'],
        localize_text((string)$chap['chapter_name']),
        $sectionIndexMap[$secName],
        isset($completedLookup[$cid]) ? 1 : 0,
        (string)$chap['chapter_name'],
    ];
}

$courseBootJson = json_encode([
    'courseId' => (int)$course['id'],
    'courseSlug' => (string)$slug,
    'coursePath' => public_lang_prefix() . course_path($course['slug']),
    'siteBaseUrl' => site_base_url(),
    'ui' => [
        'saving' => t('course.saving'),
        'completed' => t('course.completed'),
        'mark' => t('course.mark'),
        'try' => localize_text('Try it Yourself'),
        'copy' => localize_text('Copy Code'),
        'copyTree' => localize_text('Copy Tree'),
        'copyCommand' => localize_text('Copy Command'),
        'copied' => localize_text('Copied!'),
        'explore' => localize_text('Explore Structure'),
        'terminal' => localize_text('Run in Terminal'),
    ],
    'csrfToken' => csrf_token(),
    'activeChapterId' => (int)$active_chapter_id,
    'completedCount' => count($completed_chapters),
    'totalChapters' => (int)$total_chapters,
    'sections' => $syllabusSections,
    'items' => $syllabusItems,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$studioKind = '';
if ($slug === 'digital-marketing-tutorial') {
    $studioKind = 'dm';
} elseif ($slug === 'seo-tutorial') {
    $studioKind = 'seo';
} elseif ($slug === 'excel-tutorial') {
    $studioKind = 'xl';
}
$pageBodyClass = $studioKind === 'dm' ? 'dm-studio' : ($studioKind === 'seo' ? 'seo-studio' : ($studioKind === 'xl' ? 'xl-studio' : ''));
$isMarketingStudio = $studioKind === 'dm';
$isSeoStudio = $studioKind === 'seo';
$isExcelStudio = $studioKind === 'xl';
$syllabusLabel = $isMarketingStudio ? t('course.campaign_map') : ($isSeoStudio ? t('course.search_map') : ($isExcelStudio ? t('course.workbook_map') : t('course.syllabus')));
$levelLabel = $isMarketingStudio ? t('course.campaign_studio') : ($isSeoStudio ? t('course.search_studio') : ($isExcelStudio ? t('course.excel_studio') : t('course.level')));

require_once 'includes/header.php';
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />

<div class="bg-white border-b border-slate-200 py-3 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
        <nav class="flex items-center text-xs font-semibold text-slate-500">
            <a href="<?php echo h(lang_href('index')); ?>" class="hover:text-emerald-700 transition"><?php echo h(t('course.home')); ?></a>
            <span class="mx-2 text-slate-300">/</span>
            <a href="<?php echo h(lang_href('index#courses')); ?>" class="hover:text-emerald-700 transition"><?php echo h(t('course.courses')); ?></a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="text-slate-900 font-bold"><?php echo h($courseTitle); ?></span>
        </nav>
        <div class="flex items-center gap-3 text-xs font-bold text-slate-500">
            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-800 px-2.5 py-1 rounded-md border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <?php echo $total_chapters; ?> <?php echo h(t('course.lesson_count')); ?>
            </span>
            <span>&bull;</span>
            <span class="text-slate-600"><?php echo h($levelLabel); ?></span>
            <button onclick="copyToClipboard()" id="shareBtn" class="ml-2 inline-flex items-center gap-1.5 text-slate-700 hover:text-emerald-700 bg-slate-50 hover:bg-slate-100 px-3 py-1 rounded-md border border-slate-200 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                <span><?php echo h(t('course.share')); ?></span>
            </button>
            <?php if ($packageState === 'owned'): ?>
                <span class="inline-flex items-center gap-1.5 bg-emerald-600 text-white px-3 py-1 rounded-md">&check; <?php echo h(localize_text('Ad-free in your package')); ?></span>
            <?php elseif ($packageState === 'slot'): ?>
                <form method="POST" action="<?php echo h(lang_href('billing')); ?>" class="m-0">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="claim_course">
                    <input type="hidden" name="course_id" value="<?php echo (int)$course['id']; ?>">
                    <input type="hidden" name="return" value="<?php echo h(course_path($course['slug'])); ?>">
                    <button class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded-md"><?php echo h(localize_text('Add to my package')); ?></button>
                </form>
            <?php else: ?>
                <a href="<?php echo h(lang_href($packageState === 'guest' ? 'login' : 'billing?course=' . (int)$course['id'])); ?>" class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 hover:bg-amber-100 px-3 py-1 rounded-md border border-amber-200"><?php echo h(localize_text('Go ad-free from Rs 199')); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!$isMarketingStudio && !$isSeoStudio && !$isExcelStudio): ?>
<section class="course-banner" aria-labelledby="course-overview-title">
    <div class="library-shell course-banner-layout">
        <div>
            <p class="library-kicker"><?php echo h(t('course.overview')); ?></p>
            <h1 id="course-overview-title"><?php echo h($courseTitle); ?></h1>
            <p class="course-banner-description"><?php echo h(localize_text($courseDescriptionEn)); ?></p>
        </div>
        <aside class="course-overview-card" aria-labelledby="course-overview-card-title">
            <h2 id="course-overview-card-title"><?php echo h(t('course.glance')); ?></h2>
            <dl class="course-overview-metrics">
                <div><dt><?php echo h(t('course.lesson_count')); ?></dt><dd><?php echo $total_chapters; ?></dd></div>
                <div><dt><?php echo h(t('course.sections')); ?></dt><dd><?php echo count($syllabusSections); ?></dd></div>
            </dl>
            <?php if ($total_chapters > 0): ?>
                <?php
                $readerPath = $active_chapter && $requested_chapter_id === (int)$active_chapter_id
                    ? chapter_path($course['slug'], (int)$active_chapter_id, $active_chapter['chapter_name'])
                    : course_path($course['slug']);
                ?>
                <p class="course-overview-current"><span><?php echo h(t('course.current')); ?></span>
                    <strong id="course-overview-lesson"><?php echo h(localize_text((string)($active_chapter['chapter_name'] ?? $chapters[0]['chapter_name']))); ?></strong>
                </p>
                <a id="course-overview-reader" class="edu-primary-btn course-overview-link" href="<?php echo h(localized_url($readerPath)); ?>#tutorial-content"><?php echo h(t('course.open_reader')); ?></a>
            <?php else: ?>
                <p class="course-overview-current"><?php echo h(t('course.preparing')); ?></p>
            <?php endif; ?>
        </aside>
    </div>
</section>
<?php endif; ?>

<?php if ($isMarketingStudio): ?>
<section class="dm-hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="dm-kicker"><?php echo h(localize_text('Campaign studio')); ?></p>
        <div class="dm-hero-row">
            <div>
                <h1><?php echo h(localize_text('Digital Marketing')); ?></h1>
                <p><?php echo h(localize_text('Milk List, one shared household list, Rs 499 a year. Each lesson is a screen from that campaign.')); ?></p>
            </div>
            <div class="dm-metrics">
                <div><strong>60</strong><span><?php echo h(localize_text('lessons')); ?></span></div>
                <div><strong><?php echo h(localize_text('Rs 499')); ?></strong><span><?php echo h(localize_text('the offer')); ?></span></div>
                <div><strong>1</strong><span><?php echo h(localize_text('next step')); ?></span></div>
            </div>
        </div>
        <div class="dm-pills">
            <span><?php echo h(localize_text('Audience')); ?></span>
            <span><?php echo h(localize_text('Page')); ?></span>
            <span><?php echo h(localize_text('Search')); ?></span>
            <span><?php echo h(localize_text('Content')); ?></span>
            <span><?php echo h(localize_text('Email')); ?></span>
            <span><?php echo h(localize_text('Ads')); ?></span>
            <span><?php echo h(localize_text('Measure')); ?></span>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isSeoStudio): ?>
<section class="seo-hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="seo-kicker"><?php echo h(localize_text('Search studio')); ?></p>
        <div class="seo-hero-row">
            <div>
                <h1><?php echo h(localize_text('SEO')); ?></h1>
                <p><?php echo h(localize_text('Milk List, one shared household list. Each lesson is a query, the page that answers it, or the report that shows what happened.')); ?></p>
            </div>
            <div class="seo-metrics">
                <div><strong><?php echo (int)$total_chapters; ?></strong><span><?php echo h(localize_text('lessons')); ?></span></div>
                <div><strong>1</strong><span><?php echo h(localize_text('query')); ?></span></div>
                <div><strong>1</strong><span><?php echo h(localize_text('page')); ?></span></div>
            </div>
        </div>
        <div class="seo-pills">
            <span><?php echo h(localize_text('Query')); ?></span>
            <span><?php echo h(localize_text('Page')); ?></span>
            <span><?php echo h(localize_text('Index')); ?></span>
            <span><?php echo h(localize_text('Links')); ?></span>
            <span><?php echo h(localize_text('Trust')); ?></span>
            <span><?php echo h(localize_text('Measure')); ?></span>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isExcelStudio): ?>
<section class="xl-hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="xl-kicker"><?php echo h(localize_text('Excel studio')); ?></p>
        <div class="xl-hero-row">
            <div>
                <h1><?php echo h(localize_text('Excel')); ?></h1>
                <p><?php echo h(localize_text("Asha's April household spend. Each lesson is one sheet decision: a cell, a formula, a chart, a pivot, or a check you can explain.")); ?></p>
            </div>
            <div class="xl-metrics">
                <div><strong><?php echo (int)$total_chapters; ?></strong><span><?php echo h(localize_text('lessons')); ?></span></div>
                <div><strong>1</strong><span><?php echo h(localize_text('workbook')); ?></span></div>
                <div><strong><?php echo h(localize_text('April')); ?></strong><span><?php echo h(localize_text('the month')); ?></span></div>
            </div>
        </div>
        <div class="xl-pills">
            <span><?php echo h(localize_text('Grid')); ?></span>
            <span><?php echo h(localize_text('Formula')); ?></span>
            <span><?php echo h(localize_text('Lookup')); ?></span>
            <span><?php echo h(localize_text('Chart')); ?></span>
            <span><?php echo h(localize_text('Pivot')); ?></span>
            <span><?php echo h(localize_text('Query')); ?></span>
        </div>
    </div>
</section>
<?php endif; ?>

<div id="course-app" data-course="<?php echo h($courseBootJson); ?>" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">
    <div class="lesson-layout grid grid-cols-1 gap-8 items-start">
        <div class="sidebar-sticky-panel lg:sticky lg:top-[120px] lg:self-start lg:max-h-[calc(100vh-136px)] z-30">
            <button type="button" id="syllabus-toggle" aria-controls="chapter-sidebar" aria-expanded="false" class="lg:hidden w-full mb-4 bg-white border border-slate-200 text-slate-800 font-bold py-3.5 px-5 rounded-xl shadow-sm flex justify-between items-center transition hover:bg-slate-50">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    <span><?php echo h($syllabusLabel); ?></span>
                </span>
                <span class="text-slate-400 text-xs font-semibold"><?php echo h(t('course.browse')); ?></span>
            </button>

            <aside id="chapter-sidebar" class="hidden lg:flex flex-col bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden w-full max-h-[75vh] lg:max-h-[calc(100vh-130px)]">
                <?php if(isset($_SESSION['student_logged_in'])): ?>
                    <div class="bg-blue-50/50 px-5 py-5 border-b border-blue-100 flex-shrink-0">
                        <div class="flex justify-between items-end mb-3">
                            <div>
                                <span class="text-[10px] font-black text-blue-800 uppercase tracking-widest block mb-1"><?php echo h(t('course.progress')); ?></span>
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
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider"><?php echo h($syllabusLabel); ?></h3>
                    </div>
                <?php endif; ?>

                <div class="p-2.5 border-b border-gray-100 bg-slate-50">
                    <input type="text" id="sidebarTopicFilter" oninput="filterSidebarTopics()" aria-label="<?php echo h(t('course.filter')); ?>" placeholder="<?php echo h(t('course.filter_ph')); ?>" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white bg-white/80">
                </div>

                <ul class="divide-y divide-gray-100 overflow-y-auto flex-1 no-scrollbar w-full" id="chapter-list">
                    <?php if($total_chapters === 0): ?>
                        <li class="p-6 text-center text-gray-500 text-sm"><?php echo h(t('course.no_syllabus')); ?></li>
                    <?php endif; ?>
                </ul>
            </aside>
        </div>

        <div class="lesson-reader transition-all duration-300 w-full min-w-0">
            <div class="bg-white p-4 sm:p-6 md:p-10 rounded-xl md:rounded-2xl shadow-sm border border-gray-200 min-h-[600px] w-full overflow-hidden relative" id="tutorial-content">
                <div id="lesson-load-error" class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800" hidden>
                    <p role="alert"><?php echo h(t('course.load_error')); ?></p>
                    <button type="button" id="retry-lesson" class="mt-2 font-bold underline"><?php echo h(t('course.retry')); ?></button>
                </div>

                <div id="dynamic-area" aria-busy="false" class="w-full min-h-[480px]">
                    <?php if($total_chapters === 0): ?>
                        <div class="text-center py-20">
                            <div class="text-6xl mb-6">🚧</div>
                            <h2 class="text-3xl font-black text-gray-800"><?php echo h(t('course.under')); ?></h2>
                            <p class="text-lg text-gray-500 mt-2"><?php echo h(t('course.under_body')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($total_chapters > 0): ?>
                <div class="flex flex-col sm:flex-row justify-between mt-8 gap-4 w-full">
                    <button id="btn-prev" onclick="navigateChapter('prev')" class="hidden w-full sm:w-auto bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-bold py-3.5 px-8 rounded-xl shadow-sm transition flex items-center justify-center gap-2"><?php echo h(t('course.prev')); ?></button>
                    <button id="btn-next" onclick="navigateChapter('next')" class="hidden w-full sm:w-auto edu-primary-btn py-3.5 px-8 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 sm:ml-auto"><?php echo h(t('course.next')); ?></button>
                </div>
            <?php endif; ?>

            <?php if(!empty($related_courses)): ?>
                <section class="mt-12">
                    <div class="flex items-end justify-between mb-4">
                        <h2 class="text-2xl font-black text-slate-900"><?php echo h(t('course.recommended')); ?></h2>
                        <a href="<?php echo h(lang_href('index#courses')); ?>" class="text-sm font-bold text-blue-600 hover:underline"><?php echo h(t('course.view_all')); ?></a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach($related_courses as $related): ?>
                            <a href="<?php echo h(lang_href(course_path($related['slug']))); ?>" class="bg-white border border-gray-200 rounded-lg p-5 learning-surface hover:border-blue-300 transition">
                                <h3 class="font-black text-slate-900"><?php echo h(localize_text((string)$related['title'])); ?></h3>
                                <p class="text-sm text-slate-500 mt-2 line-clamp-2"><?php echo h(localize_text(seo_excerpt((string)$related['description'], 110))); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </div>
</div>

<script defer src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
<?php if ($isExcelStudio): ?>
<script src="<?php echo h(protected_asset_url('assets/js/excel-sheet.js')); ?>"></script>
<?php endif; ?>
<script src="<?php echo h(protected_asset_url('assets/js/course.js')); ?>"></script>

<?php require_once 'includes/footer.php'; ?>
