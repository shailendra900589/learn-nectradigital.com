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

    if (!$columnExists('plans', 'course_limit')) {
        $pdo->exec("ALTER TABLE plans ADD COLUMN course_limit INT NULL DEFAULT NULL AFTER duration_days");
    }
    if (!$columnExists('plans', 'offer_price')) {
        $pdo->exec("ALTER TABLE plans ADD COLUMN offer_price DECIMAL(10,2) NULL AFTER price, ADD COLUMN offer_label VARCHAR(80) NULL AFTER offer_price, ADD COLUMN offer_ends_at DATETIME NULL AFTER offer_label");
    }

    if ($tableExists('students') && !$columnExists('students', 'marketing_opt_out')) {
        $pdo->exec("ALTER TABLE students ADD COLUMN marketing_opt_out TINYINT(1) NOT NULL DEFAULT 0");
    }

    if (!$tableExists('email_campaigns')) {
        $pdo->exec("
            CREATE TABLE email_campaigns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                subject VARCHAR(190) NOT NULL,
                heading VARCHAR(190) NOT NULL,
                body MEDIUMTEXT NOT NULL,
                cta_text VARCHAR(80) NULL,
                cta_url VARCHAR(500) NULL,
                audience VARCHAR(30) NOT NULL,
                status ENUM('sending','done','cancelled') NOT NULL DEFAULT 'sending',
                total INT NOT NULL DEFAULT 0,
                sent INT NOT NULL DEFAULT 0,
                failed INT NOT NULL DEFAULT 0,
                created_by INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                finished_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    if (!$tableExists('email_queue')) {
        $pdo->exec("
            CREATE TABLE email_queue (
                id INT AUTO_INCREMENT PRIMARY KEY,
                campaign_id INT NOT NULL,
                student_id INT NOT NULL,
                email VARCHAR(190) NOT NULL,
                status ENUM('queued','sent','failed','skipped') NOT NULL DEFAULT 'queued',
                attempts INT NOT NULL DEFAULT 0,
                sent_at DATETIME NULL,
                UNIQUE KEY uniq_campaign_student (campaign_id, student_id),
                INDEX idx_queue_status (status, id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    $paymentColumns = [
        'course_id' => "ALTER TABLE payments ADD COLUMN course_id INT NULL AFTER plan_id",
        'gateway_order_id' => "ALTER TABLE payments ADD COLUMN gateway_order_id VARCHAR(64) NULL AFTER transaction_ref, ADD UNIQUE KEY uniq_payment_gateway_order (gateway_order_id)",
        'gateway_payment_id' => "ALTER TABLE payments ADD COLUMN gateway_payment_id VARCHAR(64) NULL AFTER gateway_order_id",
    ];
    foreach ($paymentColumns as $column => $sql) {
        if (!$columnExists('payments', $column)) {
            $pdo->exec($sql);
        }
    }

    if (!$tableExists('subscription_courses')) {
        $pdo->exec("
            CREATE TABLE subscription_courses (
                subscription_id INT NOT NULL,
                course_id INT NOT NULL,
                student_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (subscription_id, course_id),
                INDEX idx_subscription_course_student (student_id, course_id)
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
            $locationColumn = $pdo->query("SHOW COLUMNS FROM ads LIKE 'location'")->fetch();
            if (!str_contains((string)($locationColumn['Type'] ?? ''), "'sitewide'")) {
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
                        'sidebar',
                        'sitewide'
                    ) NOT NULL DEFAULT 'inline_mid'
                ");
            }
            $pdo->exec("UPDATE ads SET location = 'inline_mid' WHERE location = 'sidebar'");
            $pdo->exec("UPDATE ads SET location = 'inline_after_first' WHERE location = 'inline'");
            $pdo->exec("UPDATE ads SET location = 'bottom_recommendation' WHERE location IN ('top', 'bottom')");
        } catch (Throwable $e) {
            error_log('Could not upgrade ad placement schema: ' . $e->getMessage());
        }
    }

    if ($tableExists('chapters')) {
        $chapterColumns = [
            'section_name' => "ALTER TABLE chapters ADD COLUMN section_name VARCHAR(120) NOT NULL DEFAULT 'Tutorial' AFTER chapter_name",
            'quiz_json' => "ALTER TABLE chapters ADD COLUMN quiz_json MEDIUMTEXT NULL AFTER content",
            'practice_content' => "ALTER TABLE chapters ADD COLUMN practice_content MEDIUMTEXT NULL AFTER quiz_json",
            'download_file' => "ALTER TABLE chapters ADD COLUMN download_file VARCHAR(255) NULL AFTER practice_content",
            'download_label' => "ALTER TABLE chapters ADD COLUMN download_label VARCHAR(160) NULL AFTER download_file",
            'is_premium' => "ALTER TABLE chapters ADD COLUMN is_premium TINYINT(1) NOT NULL DEFAULT 0 AFTER download_label",
            'require_quiz_pass' => "ALTER TABLE chapters ADD COLUMN require_quiz_pass TINYINT(1) NOT NULL DEFAULT 1 AFTER is_premium",
            'keywords' => "ALTER TABLE chapters ADD COLUMN keywords VARCHAR(500) NULL AFTER content",
        ];
        foreach ($chapterColumns as $column => $sql) {
            if (!$columnExists('chapters', $column)) {
                $pdo->exec($sql);
            }
        }
    }

    if ($tableExists('courses') && !$columnExists('courses', 'keywords')) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN keywords VARCHAR(500) NULL AFTER description");
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
        'profile_public' => "ALTER TABLE students ADD COLUMN profile_public TINYINT(1) NOT NULL DEFAULT 0 AFTER content_access",
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
    set_platform_setting_default($pdo, 'payment_instructions', 'Pay securely online with UPI, card, net banking, or wallet. Your package starts as soon as the payment succeeds.');

    if (platform_setting($pdo, 'course_packages_seeded', '0') !== '1') {
        $packages = [
            ['Single Course', 'single-course', 199, 1, 'Pick any one course and learn it ad-free for a full year.', "Any 1 course of your choice\nAd-free lessons in that course\nDownloads and certificate for that course\nValid for 1 year"],
            ['10 Course Pack', 'ten-course-pack', 399, 10, 'Choose any 10 courses and learn all of them ad-free for a full year.', "Any 10 courses of your choice\nAd-free lessons in those courses\nDownloads and certificates for those courses\nValid for 1 year"],
            ['All Access', 'all-access', 599, null, 'Every course on Learn.Nectra, ad-free across the whole site, for a full year.', "All courses, including new ones\nAd-free across the whole site\nAll downloads and certificates\nValid for 1 year"],
        ];
        $stmtPackage = $pdo->prepare("
            INSERT INTO plans (name, slug, price, currency, duration_days, course_limit, description, features, ad_free, priority_support, downloadable_resources, certificate_access, is_active)
            VALUES (:name, :slug, :price, 'INR', 365, :course_limit, :description, :features, 1, 0, 1, 1, 1)
            ON DUPLICATE KEY UPDATE slug = slug
        ");
        foreach ($packages as [$name, $slug, $price, $limit, $description, $features]) {
            $stmtPackage->execute(['name' => $name, 'slug' => $slug, 'price' => $price, 'course_limit' => $limit, 'description' => $description, 'features' => $features]);
        }
        $pdo->exec("UPDATE plans SET is_active = 0 WHERE slug = 'premium'");
        update_platform_setting($pdo, 'payment_instructions', 'Pay securely online with UPI, card, net banking, or wallet. Your package starts as soon as the payment succeeds.');
        update_platform_setting($pdo, 'course_packages_seeded', '1');
    }
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

function active_subscriptions(PDO $pdo, int $studentId): array
{
    $stmt = $pdo->prepare("
        SELECT ss.*, p.name AS plan_name, p.slug AS plan_slug, p.course_limit, p.ad_free, p.downloadable_resources, p.certificate_access,
               (SELECT COUNT(*) FROM subscription_courses sc WHERE sc.subscription_id = ss.id) AS courses_used
        FROM student_subscriptions ss
        LEFT JOIN plans p ON p.id = ss.plan_id
        WHERE ss.student_id = :student_id
          AND ss.status = 'active'
          AND (ss.ends_at IS NULL OR ss.ends_at >= NOW())
        ORDER BY ss.ends_at DESC, ss.id DESC
    ");
    $stmt->execute(['student_id' => $studentId]);
    return $stmt->fetchAll();
}

function subscription_courses(PDO $pdo, int $subscriptionId): array
{
    $stmt = $pdo->prepare("
        SELECT c.id, c.title, c.slug
        FROM subscription_courses sc
        JOIN courses c ON c.id = sc.course_id
        WHERE sc.subscription_id = :id
        ORDER BY sc.created_at ASC, c.title ASC
    ");
    $stmt->execute(['id' => $subscriptionId]);
    return $stmt->fetchAll();
}

function student_course_access(PDO $pdo, int $studentId, int $courseId): bool
{
    $stmt = $pdo->prepare("
        SELECT 1
        FROM student_subscriptions ss
        LEFT JOIN plans p ON p.id = ss.plan_id
        WHERE ss.student_id = :student_id
          AND ss.status = 'active'
          AND (ss.ends_at IS NULL OR ss.ends_at >= NOW())
          AND (p.course_limit IS NULL OR EXISTS (
              SELECT 1 FROM subscription_courses sc WHERE sc.subscription_id = ss.id AND sc.course_id = :course_id
          ))
        LIMIT 1
    ");
    $stmt->execute(['student_id' => $studentId, 'course_id' => $courseId]);
    return (bool)$stmt->fetchColumn();
}

function student_has_ad_free(PDO $pdo, int $studentId, ?int $courseId = null): bool
{
    $stmt = $pdo->prepare("SELECT hide_ads_override FROM students WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $studentId]);
    if ((int)($stmt->fetchColumn() ?: 0) === 1) {
        return true;
    }
    foreach (active_subscriptions($pdo, $studentId) as $subscription) {
        if ((int)$subscription['ad_free'] !== 1) {
            continue;
        }
        if ($subscription['course_limit'] === null) {
            return true;
        }
        if ($courseId !== null && student_course_access($pdo, $studentId, $courseId)) {
            return true;
        }
    }
    return false;
}

function ads_course_context(?int $courseId = null): ?int
{
    static $current = null;
    if ($courseId !== null) {
        $current = $courseId;
    }
    return $current;
}

function should_show_ads(PDO $pdo): bool
{
    secure_session_start();
    if (empty($_SESSION['student_logged_in'])) {
        return true;
    }
    return !student_has_ad_free($pdo, (int)$_SESSION['student_id'], ads_course_context());
}

function plan_offer_active(array $plan): bool
{
    return isset($plan['offer_price']) && $plan['offer_price'] !== null
        && (float)$plan['offer_price'] > 0
        && (float)$plan['offer_price'] < (float)$plan['price']
        && (empty($plan['offer_ends_at']) || strtotime((string)$plan['offer_ends_at']) > time());
}

function plan_effective_price(array $plan): float
{
    return plan_offer_active($plan) ? (float)$plan['offer_price'] : (float)$plan['price'];
}

function format_rupees(float $amount): string
{
    return rtrim(rtrim(number_format($amount, 2, '.', ''), '0'), '.');
}

function plan_is_all_access(array $plan): bool
{
    return ($plan['course_limit'] ?? null) === null || $plan['course_limit'] === '';
}

function activate_plan_for_student(PDO $pdo, int $studentId, array $plan, string $note, array $courseIds = []): int
{
    $endsAt = date('Y-m-d H:i:s', time() + max(1, (int)$plan['duration_days']) * 86400);
    $stmt = $pdo->prepare("
        INSERT INTO student_subscriptions (student_id, plan_id, status, starts_at, ends_at, admin_note, updated_at)
        VALUES (:student_id, :plan_id, 'active', NOW(), :ends_at, :admin_note, NOW())
    ");
    $stmt->execute([
        'student_id' => $studentId,
        'plan_id' => (int)$plan['id'],
        'ends_at' => $endsAt,
        'admin_note' => substr($note, 0, 1000),
    ]);
    $subscriptionId = (int)$pdo->lastInsertId();
    if (!plan_is_all_access($plan)) {
        $limit = (int)$plan['course_limit'];
        foreach (array_slice(array_values(array_unique(array_map('intval', $courseIds))), 0, $limit) as $courseId) {
            add_course_to_subscription($pdo, $subscriptionId, $studentId, $courseId);
        }
    }
    return $subscriptionId;
}

function add_course_to_subscription(PDO $pdo, int $subscriptionId, int $studentId, int $courseId): bool
{
    if ($courseId <= 0) {
        return false;
    }
    $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $courseId]);
    if (!$stmt->fetchColumn()) {
        return false;
    }
    $stmt = $pdo->prepare("INSERT IGNORE INTO subscription_courses (subscription_id, course_id, student_id) VALUES (:sid, :cid, :student)");
    $stmt->execute(['sid' => $subscriptionId, 'cid' => $courseId, 'student' => $studentId]);
    return $stmt->rowCount() > 0;
}

function claim_course_slot(PDO $pdo, int $studentId, int $courseId): string
{
    if (student_course_access($pdo, $studentId, $courseId)) {
        return 'already';
    }
    foreach (array_reverse(active_subscriptions($pdo, $studentId)) as $subscription) {
        if ($subscription['course_limit'] === null) {
            continue;
        }
        if ((int)$subscription['courses_used'] < (int)$subscription['course_limit']) {
            return add_course_to_subscription($pdo, (int)$subscription['id'], $studentId, $courseId) ? 'added' : 'invalid';
        }
    }
    return 'no_slot';
}

function student_free_course_slots(PDO $pdo, int $studentId): int
{
    $free = 0;
    foreach (active_subscriptions($pdo, $studentId) as $subscription) {
        if ($subscription['course_limit'] !== null) {
            $free += max(0, (int)$subscription['course_limit'] - (int)$subscription['courses_used']);
        }
    }
    return $free;
}

function complete_gateway_payment(PDO $pdo, string $orderId, string $paymentId): bool
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE gateway_order_id = :order_id LIMIT 1 FOR UPDATE");
        $stmt->execute(['order_id' => $orderId]);
        $payment = $stmt->fetch();
        if (!$payment) {
            $pdo->rollBack();
            return false;
        }
        if ($payment['status'] === 'approved') {
            $pdo->commit();
            return true;
        }
        $stmtPlan = $pdo->prepare("SELECT * FROM plans WHERE id = :id LIMIT 1");
        $stmtPlan->execute(['id' => $payment['plan_id']]);
        $plan = $stmtPlan->fetch();
        if (!$plan) {
            $pdo->rollBack();
            return false;
        }
        $subscriptionId = activate_plan_for_student(
            $pdo,
            (int)$payment['student_id'],
            $plan,
            'Razorpay payment ' . $paymentId,
            $payment['course_id'] ? [(int)$payment['course_id']] : []
        );
        $pdo->prepare("
            UPDATE payments
            SET status = 'approved', gateway_payment_id = :payment_id, transaction_ref = :payment_ref, subscription_id = :subscription_id, paid_at = NOW()
            WHERE id = :id
        ")->execute([
            'payment_id' => $paymentId,
            'payment_ref' => $paymentId,
            'subscription_id' => $subscriptionId,
            'id' => $payment['id'],
        ]);
        $pdo->commit();
        $buyerId = (int)$payment['student_id'];
        defer_after_response(static fn() => send_package_mail($pdo, $buyerId, $subscriptionId));
        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Gateway payment completion failed: ' . $e->getMessage());
        return false;
    }
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
        'sitewide' => 'Public site + all chapters',
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
    return preg_match('#^assets/uploads/chapter-downloads/[A-Za-z0-9._-]+$#', $path) ? protected_asset_url($path) : '';
}
?>
