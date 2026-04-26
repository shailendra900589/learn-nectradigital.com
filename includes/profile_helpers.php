<?php
declare(strict_types=1);

function ensure_student_profile_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $columnExists = function (string $table, string $column) use ($pdo): bool {
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table
              AND COLUMN_NAME = :column
        ");
        $stmt->execute(['table' => $table, 'column' => $column]);
        return (int)$stmt->fetchColumn() > 0;
    };

    $studentColumns = [
        'headline' => "ALTER TABLE students ADD COLUMN headline VARCHAR(140) NULL AFTER email",
        'phone' => "ALTER TABLE students ADD COLUMN phone VARCHAR(40) NULL AFTER headline",
        'location' => "ALTER TABLE students ADD COLUMN location VARCHAR(160) NULL AFTER phone",
        'website' => "ALTER TABLE students ADD COLUMN website VARCHAR(255) NULL AFTER location",
        'linkedin' => "ALTER TABLE students ADD COLUMN linkedin VARCHAR(255) NULL AFTER website",
        'github' => "ALTER TABLE students ADD COLUMN github VARCHAR(255) NULL AFTER linkedin",
        'skills' => "ALTER TABLE students ADD COLUMN skills TEXT NULL AFTER github",
        'bio' => "ALTER TABLE students ADD COLUMN bio TEXT NULL AFTER skills",
        'profile_photo' => "ALTER TABLE students ADD COLUMN profile_photo VARCHAR(255) NULL AFTER bio",
        'public_token' => "ALTER TABLE students ADD COLUMN public_token VARCHAR(64) NULL AFTER profile_photo",
        'updated_at' => "ALTER TABLE students ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL AFTER public_token",
    ];

    foreach ($studentColumns as $column => $sql) {
        if (!$columnExists('students', $column)) {
            $pdo->exec($sql);
        }
    }

    if (!$columnExists('student_progress', 'completed_at')) {
        $pdo->exec("ALTER TABLE student_progress ADD COLUMN completed_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER course_id");
    }

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'students'
          AND INDEX_NAME = 'uniq_students_public_token'
    ");
    $stmt->execute();
    if ((int)$stmt->fetchColumn() === 0) {
        try {
            $pdo->exec("ALTER TABLE students ADD UNIQUE KEY uniq_students_public_token (public_token)");
        } catch (Throwable $e) {
            error_log('Could not add public token unique index: ' . $e->getMessage());
        }
    }
}

function student_public_token(PDO $pdo, int $studentId, ?string $existing = null): string
{
    $token = trim((string)$existing);
    if ($token !== '') {
        return $token;
    }

    do {
        $token = bin2hex(random_bytes(16));
        $stmt = $pdo->prepare("SELECT id FROM students WHERE public_token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
    } while ($stmt->fetch());

    $stmt = $pdo->prepare("UPDATE students SET public_token = :token, updated_at = NOW() WHERE id = :id");
    $stmt->execute(['token' => $token, 'id' => $studentId]);
    return $token;
}

function get_student_profile(PDO $pdo, int $studentId): ?array
{
    ensure_student_profile_schema($pdo);
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $studentId]);
    $student = $stmt->fetch();
    if (!$student) {
        return null;
    }
    $student['public_token'] = student_public_token($pdo, $studentId, $student['public_token'] ?? null);
    return $student;
}

function get_public_student_profile(PDO $pdo, string $token): ?array
{
    ensure_student_profile_schema($pdo);
    if (!preg_match('/^[a-f0-9]{32,64}$/i', $token)) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT * FROM students WHERE public_token = :token LIMIT 1");
    $stmt->execute(['token' => $token]);
    return $stmt->fetch() ?: null;
}

function student_activity_summary(PDO $pdo, int $studentId): array
{
    $stmt = $pdo->prepare("
        SELECT
            COUNT(DISTINCT c.id) AS available_courses,
            COUNT(DISTINCT CASE WHEN sp.id IS NOT NULL THEN c.id END) AS started_courses,
            COUNT(DISTINCT sp.chapter_id) AS completed_lessons,
            MAX(sp.completed_at) AS last_activity
        FROM courses c
        LEFT JOIN chapters ch ON ch.course_id = c.id
        LEFT JOIN student_progress sp ON sp.course_id = c.id AND sp.chapter_id = ch.id AND sp.student_id = :student_id
    ");
    $stmt->execute(['student_id' => $studentId]);
    $summary = $stmt->fetch() ?: [];

    $stmtCourses = $pdo->prepare("
        SELECT
            c.id,
            c.title,
            c.slug,
            COUNT(DISTINCT ch.id) AS total_lessons,
            COUNT(DISTINCT sp.chapter_id) AS completed_lessons
        FROM courses c
        LEFT JOIN chapters ch ON ch.course_id = c.id
        LEFT JOIN student_progress sp ON sp.course_id = c.id AND sp.chapter_id = ch.id AND sp.student_id = :student_id
        GROUP BY c.id
        HAVING completed_lessons > 0
        ORDER BY MAX(sp.completed_at) DESC, c.created_at DESC
        LIMIT 8
    ");
    $stmtCourses->execute(['student_id' => $studentId]);
    $courses = $stmtCourses->fetchAll();

    $completedCourses = 0;
    foreach ($courses as $course) {
        if ((int)$course['total_lessons'] > 0 && (int)$course['completed_lessons'] >= (int)$course['total_lessons']) {
            $completedCourses++;
        }
    }

    $summary['completed_courses'] = $completedCourses;
    $summary['courses'] = $courses;

    $stmtQuiz = $pdo->prepare("
        SELECT
            COUNT(*) AS quiz_attempts,
            COUNT(DISTINCT CASE WHEN passed = 1 THEN chapter_id END) AS quizzes_passed,
            AVG(score) AS average_quiz_score,
            MAX(created_at) AS last_quiz_at
        FROM student_quiz_attempts
        WHERE student_id = :student_id
    ");
    try {
        $stmtQuiz->execute(['student_id' => $studentId]);
        $quiz = $stmtQuiz->fetch() ?: [];
    } catch (Throwable $e) {
        $quiz = [];
    }
    $summary['quiz_attempts'] = (int)($quiz['quiz_attempts'] ?? 0);
    $summary['quizzes_passed'] = (int)($quiz['quizzes_passed'] ?? 0);
    $summary['average_quiz_score'] = $quiz['average_quiz_score'] !== null ? round((float)$quiz['average_quiz_score'], 1) : 0;
    $summary['last_quiz_at'] = $quiz['last_quiz_at'] ?? null;
    return $summary;
}

function profile_photo_url(?string $path): string
{
    $path = trim((string)$path);
    if ($path === '') {
        return '';
    }
    return preg_match('#^assets/uploads/students/[A-Za-z0-9._-]+$#', $path) ? $path : '';
}

function normalize_profile_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
}

function split_skills(?string $skills): array
{
    $items = preg_split('/[,\\n]+/', (string)$skills);
    $items = array_filter(array_map(fn($item) => trim($item), $items));
    return array_slice(array_values($items), 0, 16);
}
?>
