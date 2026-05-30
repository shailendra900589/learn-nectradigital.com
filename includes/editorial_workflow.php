<?php
declare(strict_types=1);

function ensure_editorial_workflow_schema(PDO $pdo): void
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

    if ($tableExists('users')) {
        $userColumns = [
            'display_name' => "ALTER TABLE users ADD COLUMN display_name VARCHAR(120) NULL AFTER username",
            'role' => "ALTER TABLE users ADD COLUMN role ENUM('owner','editor','reviewer') NOT NULL DEFAULT 'owner' AFTER password",
            'is_active' => "ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER role",
        ];
        foreach ($userColumns as $column => $sql) {
            if (!$columnExists('users', $column)) {
                $pdo->exec($sql);
            }
        }

        $ownerCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'owner'")->fetchColumn();
        if ($ownerCount === 0) {
            $firstId = (int)$pdo->query("SELECT MIN(id) FROM users")->fetchColumn();
            if ($firstId > 0) {
                $stmt = $pdo->prepare("UPDATE users SET role = 'owner' WHERE id = :id");
                $stmt->execute(['id' => $firstId]);
            }
        }
    }

    if ($tableExists('chapters')) {
        $chapterColumns = [
            'editorial_status' => "ALTER TABLE chapters ADD COLUMN editorial_status ENUM('draft','in_review','published') NOT NULL DEFAULT 'draft' AFTER order_index",
            'review_notes' => "ALTER TABLE chapters ADD COLUMN review_notes TEXT NULL AFTER editorial_status",
            'updated_by_admin_id' => "ALTER TABLE chapters ADD COLUMN updated_by_admin_id INT NULL AFTER review_notes",
            'reviewed_by_admin_id' => "ALTER TABLE chapters ADD COLUMN reviewed_by_admin_id INT NULL AFTER updated_by_admin_id",
            'reviewed_at' => "ALTER TABLE chapters ADD COLUMN reviewed_at DATETIME NULL AFTER reviewed_by_admin_id",
            'published_at' => "ALTER TABLE chapters ADD COLUMN published_at DATETIME NULL AFTER reviewed_at",
        ];
        foreach ($chapterColumns as $column => $sql) {
            if (!$columnExists('chapters', $column)) {
                $pdo->exec($sql);
            }
        }
    }
}

function current_admin_role(PDO $pdo): string
{
    secure_session_start();
    if (empty($_SESSION['admin_id'])) {
        return 'owner';
    }
    if (!empty($_SESSION['admin_role']) && in_array($_SESSION['admin_role'], ['owner', 'editor', 'reviewer'], true)) {
        return (string)$_SESSION['admin_role'];
    }

    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => (int)$_SESSION['admin_id']]);
    $role = (string)($stmt->fetchColumn() ?: 'owner');
    if (!in_array($role, ['owner', 'editor', 'reviewer'], true)) {
        $role = 'owner';
    }
    $_SESSION['admin_role'] = $role;
    return $role;
}

function require_admin_role(PDO $pdo, array $roles): void
{
    $role = current_admin_role($pdo);
    if (!in_array($role, $roles, true)) {
        http_response_code(403);
        exit('You do not have permission to access this area.');
    }
}

function admin_can_manage_platform(PDO $pdo): bool
{
    return current_admin_role($pdo) === 'owner';
}

function admin_can_edit_content(PDO $pdo): bool
{
    return in_array(current_admin_role($pdo), ['owner', 'editor'], true);
}

function admin_can_review_content(PDO $pdo): bool
{
    return in_array(current_admin_role($pdo), ['owner', 'reviewer'], true);
}
?>
