<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/utility.php';
session_start();

$id = $_REQUEST['user-id'];
$password = $_REQUEST['password-login'];

$conn = connect_to_database();
$sql = "SELECT * FROM saje5795_goralys.users WHERE user_id = ?;";
$stmt = $conn -> prepare($sql);
$stmt -> bind_param("s", $id);

if ($stmt->execute()) {
    $result = $stmt -> get_result();
    $response = $result -> fetch_assoc();
    $hashed_password = $response['password_hash'];
} else {
    http_response_code(403); // Access unauthorized
    error_log("An error occured while trying to connect to database");
    echo json_encode(["progress" => "No ID found"]);

    exit(1);
}

if (password_verify($password, $hashed_password)) {
    $user = $response;
    $_SESSION['user-email'] = $user['email'];
    $_SESSION['user-id'] = $user['user_id'];
    $_SESSION['user-name'] = $user['full_name'];
    $_SESSION['user-type'] = $user['role'];
    $_SESSION['logged-in'] = true;

    $get_topics_query = "SELECT * FROM saje5795_goralys.student_topics WHERE student_id = ?";
    $get_topics_stmt = $conn->prepare($get_topics_query);
    $get_topics_stmt->bind_param("s", $user['user_id']);
    $get_topics_stmt->execute();
    $result = $get_topics_stmt->get_result();

    // We know that a student has exactly two topics, so we can just fetch_assoc() twice.
    $row = $result->fetch_assoc();
    cache_student_topics_info($row['topic_id'], 1);
    $row = $result->fetch_assoc();
    cache_student_topics_info($row['topic_id'], 2);

    $stmt->close();
    $conn->close();

    show_toast('success',
    "Connexion réussie",
    "Vous avez bien été connecté à votre compte.");

} else {
    $stmt->close();
    $conn->close();

    show_toast('error',
        "Echec de la connexion",
        "Email ou mot de passe invalide.",
        "login.html");
}

http_response_code(200);
exit();
