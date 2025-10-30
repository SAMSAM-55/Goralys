<?php

session_start();

$logged_in = $_SESSION['logged-in'] ?? false;
$username = $_SESSION['user-name'] ?? null;
$user_email = $_SESSION['user-email'] ?? null;
$user_id = $_SESSION['user-id'] ?? null;
$user_type = $_SESSION['user-type'] ?? null;

$user_topic_1 = $_SESSION['user-topic-1'] ?? null;
$user_teacher_1 = $_SESSION['user-teacher-1'] ?? null;

$user_topic_2 = $_SESSION['user-topic-2'] ?? null;
$user_teacher_2 = $_SESSION['user-teacher-2'] ?? null;

echo json_encode([
        'logged_in' => $logged_in,
        'username' => $username,
        'user_email' => $user_email,
        'user_id' => $user_id,
        'user_type' => $user_type,
        'user_topic_1' => $user_topic_1,
        'user_teacher_1' => $user_teacher_1,
        'user_topic_2' => $user_topic_2,
        'user_teacher_2' => $user_teacher_2]);
