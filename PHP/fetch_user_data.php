<?php

session_start();

$loggedIn = $_SESSION['logged-in'] ?? false;
$userName = $_SESSION['user-name'] ?? null;
$userEmail = $_SESSION['user-email'] ?? null;
$userId = $_SESSION['user-id'] ?? null;
$userType = $_SESSION['user-type'] ?? null;

$userTopic1 = $_SESSION['user-topic-1'] ?? null;
$userTeacher1 = $_SESSION['user-teacher-1'] ?? null;

$userTopic2 = $_SESSION['user-topic-2'] ?? null;
$userTeacher2 = $_SESSION['user-teacher-2'] ?? null;

echo json_encode([
        'logged_in' => $loggedIn,
        'username' => $userName,
        'user_email' => $userEmail,
        'user_id' => $userId,
        'user_type' => $userType,
        'user_topic_1' => $userTopic1,
        'user_teacher_1' => $userTeacher1,
        'user_topic_2' => $userTopic2,
        'user_teacher_2' => $userTeacher2]);
