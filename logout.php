<?php
require_once 'includes/db.php';
secure_session_start();
// Unset all student session variables
unset($_SESSION['student_logged_in']);
unset($_SESSION['student_id']);
unset($_SESSION['student_name']);
header('Location: ' . app_path('index'));
exit;
?>
