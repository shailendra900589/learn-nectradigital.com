<?php
require_once 'includes/db.php';
enforce_request_budget('live-search', 90, 60);
secure_session_start();

if (isset($_GET['q'])) {
    $search_term = trim($_GET['q']);
    
    if ($search_term === '' || strlen($search_term) > 80) {
        exit;
    }

    $q = like_contains($search_term);
    
    // Search chapters with their parent course (name, keywords, section, or content)
    $stmtChap = $pdo->prepare("
        SELECT ch.id as chapter_id, ch.chapter_name, ch.section_name, c.id as course_id, c.title as course_title, c.slug as course_slug
        FROM chapters ch
        JOIN courses c ON ch.course_id = c.id
        LEFT JOIN course_rank r ON r.course_id = c.id
        WHERE ch.is_premium = 0 AND (ch.chapter_name LIKE :q1 ESCAPE '\\\\' OR ch.keywords LIKE :q2 ESCAPE '\\\\' OR ch.section_name LIKE :q3 ESCAPE '\\\\' OR ch.content LIKE :q4 ESCAPE '\\\\')
        ORDER BY (ch.chapter_name LIKE :qExact1) DESC, (ch.keywords LIKE :qExact2) DESC, " . course_rank_heat_sql('r') . " DESC, ch.id ASC
        LIMIT 8
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

    // Search courses
    $stmtCourse = $pdo->prepare("SELECT c.id, c.title, c.slug FROM courses c LEFT JOIN course_rank r ON r.course_id = c.id WHERE c.title LIKE :q1 ESCAPE '\\\\' OR c.keywords LIKE :q2 ESCAPE '\\\\' OR c.description LIKE :q3 ESCAPE '\\\\' ORDER BY (c.title LIKE :qExact) DESC, " . course_rank_heat_sql('r') . " DESC, c.id ASC LIMIT 3");
    $stmtCourse->execute(['q1' => $q, 'q2' => $q, 'q3' => $q, 'qExact' => $q]);
    $courses = $stmtCourse->fetchAll();

    if (course_rank_query_counts($search_term)) {
        $rankedIds = [];
        foreach ($courses as $row) {
            $rankedIds[] = (int)$row['id'];
        }
        foreach ($chapters as $row) {
            $rankedIds[] = (int)$row['course_id'];
        }
        record_course_signals($pdo, $rankedIds, 'search');
    }

    if (current_lang() !== 'en' && (!empty($chapters) || !empty($courses))) {
        $searchBag = [];
        foreach ($chapters as $row) {
            $searchBag[] = (string)$row['chapter_name'];
            $searchBag[] = (string)$row['course_title'];
        }
        foreach ($courses as $row) {
            $searchBag[] = (string)$row['title'];
        }
        localize_texts($searchBag);
    }

    if (!empty($chapters) || !empty($courses)) {
        if (!empty($chapters)) {
            echo '<div class="px-3 py-1.5 bg-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-500 border-b border-slate-200">' . h(t('search.ajax_lessons')) . '</div>';
            foreach ($chapters as $row) {
                $targetUrl = lang_href(course_path($row['course_slug']) . '/' . (int)$row['chapter_id'] . '-' . slugify($row['chapter_name'], 'chapter'));
                echo '<a href="' . h($targetUrl) . '" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-900 border-b border-slate-50 last:border-0 transition-colors flex items-center justify-between group">';
                echo '<span class="font-semibold flex items-center gap-2">';
                echo '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>';
                echo h(localize_text((string)$row['chapter_name']));
                echo '</span>';
                echo '<span class="text-[11px] font-bold text-slate-400 group-hover:text-emerald-600 bg-slate-50 group-hover:bg-white border border-slate-200 px-2 py-0.5 rounded">' . h(localize_text((string)$row['course_title'])) . '</span>';
                echo '</a>';
            }
        }
        if (!empty($courses)) {
            echo '<div class="px-3 py-1.5 bg-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-500 border-b border-slate-200">' . h(t('search.ajax_tracks')) . '</div>';
            foreach ($courses as $row) {
                echo '<a href="' . h(lang_href(course_path($row['slug']))) . '" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-900 border-b border-slate-50 last:border-0 transition-colors flex items-center gap-2">';
                echo '<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>';
                echo '<span class="font-bold">' . h(localize_text((string)$row['title'])) . '</span>';
                echo '</a>';
            }
        }
    } else {
        echo '<div class="px-4 py-4 text-sm text-slate-500 italic text-center">' . h(t('search.ajax_none')) . '</div>';
    }
}
