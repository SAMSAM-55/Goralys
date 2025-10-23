<?php
session_start();

$user_type = $_SESSION['user-type'] ?? null;

echo $user_type;

switch ($user_type) {
    case 'admin':
        header('Location: ../subject-admin.html');
        exit();
    case 'teacher':
        header('Location: ../subject-teacher.html');
        exit();
    case 'student':
        header('Location: ../subject-student.html');
        exit();
    default:
        header('Location: ../index.html');
        exit();
}