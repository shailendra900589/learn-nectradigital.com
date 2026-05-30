<?php
require_once 'includes/db.php';
secure_session_start();

if (empty($_SESSION['student_logged_in'])) {
    json_response(['status' => 'error', 'message' => 'Please log in.'], 401);
}

$studentId = (int)$_SESSION['student_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

if ($action === 'mark_read') {
    verify_csrf();
    $ids = $_POST['ids'] ?? [];
    $ids = is_array($ids) ? $ids : [];
    mark_notifications_read($pdo, $studentId, $ids);
    json_response(['status' => 'success', 'unread' => unread_notifications_count($pdo, $studentId)]);
}

if ($action === 'list') {
    $items = recent_notifications($pdo, $studentId, 10);
    json_response([
        'status' => 'success',
        'unread' => unread_notifications_count($pdo, $studentId),
        'items' => $items,
    ]);
}

json_response(['status' => 'error', 'message' => 'Unsupported action.'], 400);
?>
