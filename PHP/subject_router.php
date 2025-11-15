<?php

session_start();

$userType = $_SESSION['user-type'] ?? null;

switch ($userType) {
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
