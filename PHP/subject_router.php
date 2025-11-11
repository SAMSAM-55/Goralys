<?php

session_start();

$user_type = $_SESSION['user-type'] ?? null;

switch ($user_type) {
    case 'admin':
        header('Location: ../subject-admin_page.php');
        exit();
    case 'teacher':
        header('Location: ../subject-teacher_page.php');
        exit();
    case 'student':
        header('Location: ../subject-student_page.php');
        exit();
    default:
        header('Location: ../index.html');
        exit();
}
