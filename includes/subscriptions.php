<?php
declare(strict_types=1);

function ensure_subscription_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $tableExists = function (string $table) use ($pdo): bool {
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table
        ");
        $stmt->execute(['table' => $table]);
        return (int)$stmt->fetchColumn() > 0;
    };

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

    if (!$tableExists('plans')) {
        $pdo->exec("
            CREATE TABLE plans (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                slug VARCHAR(140) NOT NULL UNIQUE,
                price DECIMAL(10,2) NOT NULL DEFAULT 0,
                currency VARCHAR(8) NOT NULL DEFAULT 'INR',
                duration_days INT NOT NULL DEFAULT 30,
                description TEXT NULL,
                features TEXT NULL,
                ad_free TINYINT(1) NOT NULL DEFAULT 1,
                priority_support TINYINT(1) NOT NULL DEFAULT 0,
                downloadable_resources TINYINT(1) NOT NULL DEFAULT 0,
                certificate_access TINYINT(1) NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('student_subscriptions')) {
        $pdo->exec("
            CREATE TABLE student_subscriptions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NOT NULL,
                plan_id INT NULL,
                status ENUM('pending','active','paused','expired','cancelled') NOT NULL DEFAULT 'pending',
                starts_at DATETIME NULL,
                ends_at DATETIME NULL,
                admin_note TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                INDEX idx_student_subscription (student_id, status),
                INDEX idx_subscription_plan (plan_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('payments')) {
        $pdo->exec("
            CREATE TABLE payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NOT NULL,
                plan_id INT NULL,
                subscription_id INT NULL,
                amount DECIMAL(10,2) NOT NULL DEFAULT 0,
                currency VARCHAR(8) NOT NULL DEFAULT 'INR',
                method VARCHAR(80) NULL,
                transaction_ref VARCHAR(160) NULL,
                status ENUM('requested','paid','approved','rejected','refunded') NOT NULL DEFAULT 'requested',
                paid_at DATETIME NULL,
                admin_note TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_payment_student (student_id, created_at),
                INDEX idx_payment_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('platform_settings')) {
        $pdo->exec("
            CREATE TABLE platform_settings (
                setting_key VARCHAR(80) PRIMARY KEY,
                setting_value TEXT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if ($tableExists('ads') && $columnExists('ads', 'location')) {
        try {
            $pdo->exec("
                ALTER TABLE ads
                MODIFY location ENUM(
                    'inline_after_first',
                    'inline_mid',
                    'inline_before_summary',
                    'text_link',
                    'sponsored_note',
                    'resource_box',
                    'cta_card',
                    'code_break',
                    'quiz_prompt',
                    'bottom_recommendation',
                    'inline',
                    'top',
                    'bottom',
                    'sidebar'
                ) NOT NULL DEFAULT 'inline_mid'
            ");
            $pdo->exec("UPDATE ads SET location = 'inline_mid' WHERE location = 'sidebar'");
            $pdo->exec("UPDATE ads SET location = 'inline_after_first' WHERE location = 'inline'");
            $pdo->exec("UPDATE ads SET location = 'bottom_recommendation' WHERE location IN ('top', 'bottom')");
        } catch (Throwable $e) {
            error_log('Could not upgrade ad placement schema: ' . $e->getMessage());
        }
    }

    if ($tableExists('chapters')) {
        $chapterColumns = [
            'quiz_json' => "ALTER TABLE chapters ADD COLUMN quiz_json MEDIUMTEXT NULL AFTER content",
            'practice_content' => "ALTER TABLE chapters ADD COLUMN practice_content MEDIUMTEXT NULL AFTER quiz_json",
            'download_file' => "ALTER TABLE chapters ADD COLUMN download_file VARCHAR(255) NULL AFTER practice_content",
            'download_label' => "ALTER TABLE chapters ADD COLUMN download_label VARCHAR(160) NULL AFTER download_file",
            'is_premium' => "ALTER TABLE chapters ADD COLUMN is_premium TINYINT(1) NOT NULL DEFAULT 0 AFTER download_label",
            'require_quiz_pass' => "ALTER TABLE chapters ADD COLUMN require_quiz_pass TINYINT(1) NOT NULL DEFAULT 1 AFTER is_premium",
        ];
        foreach ($chapterColumns as $column => $sql) {
            if (!$columnExists('chapters', $column)) {
                $pdo->exec($sql);
            }
        }
    }

    if ($tableExists('student_progress')) {
        $progressColumns = [
            'quiz_score' => "ALTER TABLE student_progress ADD COLUMN quiz_score DECIMAL(5,2) NULL AFTER completed_at",
            'quiz_passed' => "ALTER TABLE student_progress ADD COLUMN quiz_passed TINYINT(1) NOT NULL DEFAULT 0 AFTER quiz_score",
        ];
        foreach ($progressColumns as $column => $sql) {
            if (!$columnExists('student_progress', $column)) {
                $pdo->exec($sql);
            }
        }

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'student_progress'
              AND INDEX_NAME = 'uniq_student_chapter_progress'
        ");
        $stmt->execute();
        if ((int)$stmt->fetchColumn() === 0) {
            try {
                $pdo->exec("ALTER TABLE student_progress ADD UNIQUE KEY uniq_student_chapter_progress (student_id, chapter_id)");
            } catch (Throwable $e) {
                error_log('Could not add student progress unique index: ' . $e->getMessage());
            }
        }
    }

    if (!$tableExists('student_quiz_attempts')) {
        $pdo->exec("
            CREATE TABLE student_quiz_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NOT NULL,
                course_id INT NOT NULL,
                chapter_id INT NOT NULL,
                score DECIMAL(5,2) NOT NULL DEFAULT 0,
                correct_count INT NOT NULL DEFAULT 0,
                total_questions INT NOT NULL DEFAULT 0,
                answers_json MEDIUMTEXT NULL,
                passed TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_quiz_student_chapter (student_id, chapter_id),
                INDEX idx_quiz_course (course_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    if (!$tableExists('otp_verifications')) {
        $pdo->exec("
            CREATE TABLE otp_verifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(190) NOT NULL,
                student_id INT NULL,
                purpose ENUM('registration','password_reset') NOT NULL,
                otp_hash VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                attempts INT NOT NULL DEFAULT 0,
                consumed_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_otp_lookup (email, purpose, consumed_at),
                INDEX idx_otp_student (student_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    $studentColumns = [
        'content_access' => "ALTER TABLE students ADD COLUMN content_access TINYINT(1) NOT NULL DEFAULT 1 AFTER password",
        'profile_public' => "ALTER TABLE students ADD COLUMN profile_public TINYINT(1) NOT NULL DEFAULT 1 AFTER content_access",
        'public_email' => "ALTER TABLE students ADD COLUMN public_email TINYINT(1) NOT NULL DEFAULT 0 AFTER profile_public",
        'public_phone' => "ALTER TABLE students ADD COLUMN public_phone TINYINT(1) NOT NULL DEFAULT 0 AFTER public_email",
        'hide_ads_override' => "ALTER TABLE students ADD COLUMN hide_ads_override TINYINT(1) NOT NULL DEFAULT 0 AFTER public_phone",
        'account_status' => "ALTER TABLE students ADD COLUMN account_status ENUM('active','paused','blocked') NOT NULL DEFAULT 'active' AFTER hide_ads_override",
        'email_verified' => "ALTER TABLE students ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 1 AFTER account_status",
        'email_verified_at' => "ALTER TABLE students ADD COLUMN email_verified_at DATETIME NULL AFTER email_verified",
    ];

    foreach ($studentColumns as $column => $sql) {
        if ($tableExists('students') && !$columnExists('students', $column)) {
            $pdo->exec($sql);
        }
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM plans WHERE slug = :slug");
    $stmt->execute(['slug' => 'premium']);
    if ((int)$stmt->fetchColumn() === 0) {
        $stmtInsert = $pdo->prepare("
            INSERT INTO plans (name, slug, price, currency, duration_days, description, features, ad_free, priority_support, downloadable_resources, certificate_access, is_active)
            VALUES (:name, :slug, :price, :currency, :duration_days, :description, :features, 1, 1, 1, 1, 1)
        ");
        $stmtInsert->execute([
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => 499,
            'currency' => 'INR',
            'duration_days' => 30,
            'description' => 'Ad-free learning with premium access controls, priority support, downloads, and certificates.',
            'features' => "Ad-free course pages\nPremium profile badge\nPriority support\nDownloadable resources\nCertificate access",
        ]);
    }

    set_platform_setting_default($pdo, 'plans_enabled', '1');
    set_platform_setting_default($pdo, 'payment_instructions', 'Pay using your preferred method and submit the transaction ID. Admin will verify and activate your plan.');
}

function set_platform_setting_default(PDO $pdo, string $key, string $value): void
{
    $stmt = $pdo->prepare("INSERT IGNORE INTO platform_settings (setting_key, setting_value, updated_at) VALUES (:k, :v, NOW())");
    $stmt->execute(['k' => $key, 'v' => $value]);
}

function platform_setting(PDO $pdo, string $key, string $default = ''): string
{
    $stmt = $pdo->prepare("SELECT setting_value FROM platform_settings WHERE setting_key = :k LIMIT 1");
    $stmt->execute(['k' => $key]);
    $value = $stmt->fetchColumn();
    return $value === false ? $default : (string)$value;
}

function update_platform_setting(PDO $pdo, string $key, string $value): void
{
    $stmt = $pdo->prepare("
        INSERT INTO platform_settings (setting_key, setting_value, updated_at)
        VALUES (:k, :v, NOW())
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()
    ");
    $stmt->execute(['k' => $key, 'v' => $value]);
}

function plans_enabled(PDO $pdo): bool
{
    return platform_setting($pdo, 'plans_enabled', '1') === '1';
}

function active_subscription(PDO $pdo, int $studentId): ?array
{
    $stmt = $pdo->prepare("
        SELECT ss.*, p.name AS plan_name, p.slug AS plan_slug, p.ad_free, p.priority_support, p.downloadable_resources, p.certificate_access
        FROM student_subscriptions ss
        LEFT JOIN plans p ON p.id = ss.plan_id
        WHERE ss.student_id = :student_id
          AND ss.status = 'active'
          AND (ss.ends_at IS NULL OR ss.ends_at >= NOW())
        ORDER BY ss.ends_at DESC, ss.id DESC
        LIMIT 1
    ");
    $stmt->execute(['student_id' => $studentId]);
    return $stmt->fetch() ?: null;
}

function student_can_view_content(PDO $pdo, int $studentId): bool
{
    $stmt = $pdo->prepare("SELECT content_access, account_status FROM students WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $studentId]);
    $student = $stmt->fetch();
    return $student && (int)$student['content_access'] === 1 && $student['account_status'] === 'active';
}

function student_has_ad_free(PDO $pdo, int $studentId): bool
{
    $stmt = $pdo->prepare("SELECT hide_ads_override FROM students WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $studentId]);
    $override = (int)($stmt->fetchColumn() ?: 0);
    if ($override === 1) {
        return true;
    }
    $subscription = active_subscription($pdo, $studentId);
    return $subscription && (int)$subscription['ad_free'] === 1;
}

function should_show_ads(PDO $pdo): bool
{
    secure_session_start();
    if (empty($_SESSION['student_logged_in'])) {
        return true;
    }
    return !student_has_ad_free($pdo, (int)$_SESSION['student_id']);
}

function plan_features(?string $features): array
{
    $items = preg_split('/[\r\n]+/', (string)$features);
    $items = array_filter(array_map(fn($item) => trim($item), $items));
    return array_slice(array_values($items), 0, 12);
}

function content_ad_locations(): array
{
    return [
        'inline_after_first' => 'Inline after first paragraph',
        'inline_mid' => 'Inline middle paragraph',
        'inline_before_summary' => 'Before lesson summary',
        'text_link' => 'Native text link',
        'sponsored_note' => 'Sponsored note',
        'resource_box' => 'Resource recommendation',
        'cta_card' => 'CTA card',
        'code_break' => 'Code break sponsor',
        'quiz_prompt' => 'Quiz prompt sponsor',
        'bottom_recommendation' => 'Bottom recommendation',
    ];
}

function parse_quiz_lines(string $lines): array
{
    $quiz = [];
    foreach (preg_split('/\R+/', trim($lines)) as $line) {
        $parts = array_values(array_filter(array_map('trim', explode('|', $line)), fn($part) => $part !== ''));
        if (count($parts) < 3) {
            continue;
        }
        $question = array_shift($parts);
        $correct = array_shift($parts);
        $options = array_values(array_unique(array_merge([$correct], $parts)));
        if (count($options) < 2) {
            continue;
        }
        $quiz[] = [
            'question' => substr($question, 0, 500),
            'answer' => substr($correct, 0, 300),
            'options' => array_slice(array_map(fn($option) => substr($option, 0, 300), $options), 0, 6),
        ];
    }
    return array_slice($quiz, 0, 25);
}

function quiz_to_lines(?string $json): string
{
    $quiz = json_decode((string)$json, true);
    if (!is_array($quiz)) {
        return '';
    }
    $lines = [];
    foreach ($quiz as $item) {
        $question = trim((string)($item['question'] ?? ''));
        $answer = trim((string)($item['answer'] ?? ''));
        $options = array_values(array_filter((array)($item['options'] ?? []), fn($option) => trim((string)$option) !== '' && trim((string)$option) !== $answer));
        if ($question !== '' && $answer !== '') {
            $lines[] = implode(' | ', array_merge([$question, $answer], $options));
        }
    }
    return implode("\n", $lines);
}

function chapter_download_url(?string $path): string
{
    $path = trim((string)$path);
    if ($path === '') {
        return '';
    }
    return preg_match('#^assets/uploads/chapter-downloads/[A-Za-z0-9._-]+$#', $path) ? $path : '';
}
?>
