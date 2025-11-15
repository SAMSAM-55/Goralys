<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/utility.php';

use Goralys\Config\Config;
use Goralys\Utility\GoralysUtility;

Config::init();

session_start();

if (!GoralysUtility::verifyCSRF()) {
    die("Invalid CSRF Token");
}

$id = $_REQUEST['user-id'];
$password = $_REQUEST['password-login'];

$conn = Config::connectToDatabase();
$sql = "SELECT * FROM saje5795_goralys.users WHERE user_id = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $response = $result->fetch_assoc();
    $hashedPassword = $response['password_hash'];
} else {
    http_response_code(403); // Access unauthorized
    error_log("An error occured while trying to connect to database");
    echo json_encode(["progress" => "No ID found"]);

    exit(1);
}

if (password_verify($password, $hashedPassword)) {
    $user = $response;
    $_SESSION['user-email'] = $user['email'];
    $_SESSION['user-id'] = $user['user_id'];
    $_SESSION['user-name'] = $user['full_name'];
    $_SESSION['user-type'] = $user['role'];
    $_SESSION['logged-in'] = true;

    $getTopicsRequest = "SELECT * FROM saje5795_goralys.student_topics WHERE student_id = ?";
    $getTopicsStmt = $conn->prepare($getTopicsRequest);
    $getTopicsStmt->bind_param("s", $user['user_id']);
    $getTopicsStmt->execute();
    $result = $getTopicsStmt->get_result();

    // We know that a student has exactly two topics, so we can just fetch_assoc() twice.
    $row = $result->fetch_assoc();
    GoralysUtility::cacheStudentTopicsInfo($row['topic_id'], 1);
    $row = $result->fetch_assoc();
    GoralysUtility::cacheStudentTopicsInfo($row['topic_id'], 2);

    $stmt->close();
    $conn->close();

    GoralysUtility::showToast(
        'success',
        "Connexion réussie",
        "Vous avez bien été connecté à votre compte."
    );
} else {
    $stmt->close();
    $conn->close();

    GoralysUtility::showToast(
        'error',
        "Echec de la connexion",
        "Email ou mot de passe invalide.",
        "login_page.php"
    );
}

http_response_code(200);
exit();
