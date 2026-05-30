<?php
declare(strict_types=1);

function recommendation_tokens(string $text): array
{
    $text = strtolower(plain_text($text));
    $text = preg_replace('/[^a-z0-9\s]+/', ' ', $text);
    $parts = preg_split('/\s+/', trim((string)$text)) ?: [];
    $stopwords = [
        'the', 'and', 'for', 'with', 'from', 'this', 'that', 'your', 'you', 'are', 'was', 'were', 'into', 'course',
        'tutorial', 'learn', 'lesson', 'lessons', 'how', 'what', 'when', 'where', 'why', 'step', 'guide', 'using',
        'use', 'about', 'build', 'free', 'programming', 'coding',
    ];
    $stopLookup = array_flip($stopwords);
    $tokens = [];
    foreach ($parts as $part) {
        if (strlen($part) < 3 || isset($stopLookup[$part])) {
            continue;
        }
        $tokens[$part] = true;
    }
    return array_keys($tokens);
}

function recommendation_reason_details(array $course, array $matchedTokens, int $popularity, int $freshnessDays): array
{
    $details = [];
    if ($matchedTokens) {
        $details[] = 'Matched topics: ' . implode(', ', array_slice($matchedTokens, 0, 4));
    }
    if ($popularity > 0) {
        $details[] = 'Trending score from ' . number_format($popularity) . ' learner activity signal(s)';
    }
    if ($freshnessDays <= 14) {
        $details[] = 'Fresh content added ' . $freshnessDays . ' day(s) ago';
    }
    if (!$details) {
        $details[] = 'General recommendation based on course quality and recency';
    }
    return $details;
}

function recommended_courses_for_student(PDO $pdo, int $studentId, int $limit = 6): array
{
    $limit = max(1, min(12, $limit));

    $stmtHistory = $pdo->prepare("
        SELECT c.id, c.title, c.description, MAX(sp.completed_at) AS last_activity
        FROM student_progress sp
        INNER JOIN courses c ON c.id = sp.course_id
        WHERE sp.student_id = :student_id
        GROUP BY c.id
        ORDER BY last_activity DESC
        LIMIT 12
    ");
    $stmtHistory->execute(['student_id' => $studentId]);
    $history = $stmtHistory->fetchAll() ?: [];

    $historyIds = array_map(fn($row) => (int)$row['id'], $history);
    $interestTokens = [];
    foreach ($history as $row) {
        foreach (recommendation_tokens((string)$row['title'] . ' ' . (string)$row['description']) as $token) {
            $interestTokens[$token] = ($interestTokens[$token] ?? 0) + 1;
        }
    }

    $stmtCandidates = $pdo->query("
        SELECT c.id, c.title, c.slug, c.description, c.created_at,
               COUNT(DISTINCT ch.id) AS chapter_count,
               COUNT(DISTINCT sp.id) AS popularity
        FROM courses c
        LEFT JOIN chapters ch ON ch.course_id = c.id
        LEFT JOIN student_progress sp ON sp.course_id = c.id
        GROUP BY c.id
        ORDER BY popularity DESC, c.created_at DESC
        LIMIT 120
    ");
    $candidates = $stmtCandidates->fetchAll() ?: [];

    $scored = [];
    foreach ($candidates as $course) {
        $courseId = (int)$course['id'];
        if (in_array($courseId, $historyIds, true)) {
            continue;
        }

        $tokens = recommendation_tokens((string)$course['title'] . ' ' . (string)$course['description']);
        $overlapScore = 0;
        $matchedTokens = [];
        foreach ($tokens as $token) {
            $tokenScore = $interestTokens[$token] ?? 0;
            $overlapScore += $tokenScore;
            if ($tokenScore > 0) {
                $matchedTokens[] = $token;
            }
        }

        $popularity = (int)$course['popularity'];
        $freshnessDays = max(1, (int)floor((time() - strtotime((string)$course['created_at'])) / 86400));
        $freshnessScore = max(0, 30 - min(30, $freshnessDays));

        $score = ($overlapScore * 7) + ($popularity * 1.6) + ($freshnessScore * 0.8);
        $course['recommendation_score'] = round($score, 2);
        $course['recommendation_reason'] = $overlapScore > 0
            ? 'Matches your recent learning topics'
            : ($popularity > 0 ? 'Trending among learners' : 'Freshly added tutorial');
        $course['recommendation_reason_details'] = recommendation_reason_details($course, $matchedTokens, $popularity, $freshnessDays);
        $course['recommendation_matched_tokens'] = array_slice(array_values(array_unique($matchedTokens)), 0, 6);
        $course['recommendation_factors'] = [
            'overlap' => $overlapScore,
            'popularity' => $popularity,
            'freshness_days' => $freshnessDays,
        ];
        $scored[] = $course;
    }

    usort($scored, fn($a, $b) => $b['recommendation_score'] <=> $a['recommendation_score']);
    return array_slice($scored, 0, $limit);
}

function maybe_emit_personalized_recommendation(PDO $pdo, int $studentId): void
{
    $stmtRecent = $pdo->prepare("
        SELECT id
        FROM student_notifications
        WHERE student_id = :student_id
          AND type = 'recommendation'
          AND created_at >= DATE_SUB(NOW(), INTERVAL 18 HOUR)
        LIMIT 1
    ");
    $stmtRecent->execute(['student_id' => $studentId]);
    if ($stmtRecent->fetch()) {
        return;
    }

    $recommended = recommended_courses_for_student($pdo, $studentId, 1);
    if (!$recommended) {
        return;
    }

    $item = $recommended[0];
    create_notification(
        $pdo,
        $studentId,
        'Personalized recommendation',
        'Try ' . (string)$item['title'] . '. ' . (string)$item['recommendation_reason'] . '.',
        'recommendation',
        site_base_url() . course_path((string)$item['slug']),
        [
            'score' => $item['recommendation_score'],
            'reason' => $item['recommendation_reason'],
            'reason_details' => $item['recommendation_reason_details'] ?? [],
            'matched_tokens' => $item['recommendation_matched_tokens'] ?? [],
            'factors' => $item['recommendation_factors'] ?? [],
        ]
    );
}
?>
