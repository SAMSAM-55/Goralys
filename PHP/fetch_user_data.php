<?php
session_start();

$logged_in = $_SESSION['logged-in'] ?? false;
$username = $_SESSION['user-name'] ?? null;
$user_email = $_SESSION['user-email'] ?? null;
$user_id = $_SESSION['user-id'] ?? null;
$user_type = $_SESSION['user-type'] ?? null;

echo json_encode(['logged_in' => $logged_in, 'username' => $username, 'user_email' => $user_email, 'user_id' => $user_id, 'user_type' => $user_type]);