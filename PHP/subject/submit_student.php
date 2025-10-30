<?php

use Goralys\Config\Config;
use Goralys\Utility\GoralysUtility;

session_start();

// Read the JSON payload sent by the JS script (see core.j, section  student functions)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!GoralysUtility::verifyCSRF($data['csrf-token'])) {
    die("Invalid CSRF Token");
}

// Doesn't mind the subject status (impossible to post if the subject is uneditable)
$subject_index = (string)$data['subject-index'] ?? null;
$student_id = $_SESSION['user-id'] ?? null;
$topic_id = $_SESSION['user-topic-id-' . $subject_index] ?? null;

// Variables validation
$subject_index = is_string($subject_index) ? trim($subject_index) : null;
$student_id = is_string($student_id) ? trim($student_id) : null;
$topic_id = is_scalar($topic_id) ? $topic_id : null;

if (!$topic_id || !$student_id || !$subject_index) {
    GoralysUtility::showToast(
        'error',
        "Soumission",
        "Une erreur interne est survenue lors de la soumission du sujet. Veuillez réessayer ultérieurement",
        "subject-student_page.php",
        js: true
    );
    http_response_code(400); // Bad request
    exit(1);
}

$conn = Config::connectToDatabase();
$query = "UPDATE saje5795_goralys.student_topics SET subject_status = 1 WHERE student_id = ? AND topic_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $student_id, $topic_id);

if (!$stmt->execute()) {
    GoralysUtility::showToast(
        'error',
        "Soumission",
        "Une erreur interne est survenue lors de la soumission du sujet. Veuillez réessayer ultérieurement",
        js: true
    );
    http_response_code(500); // Internal server error
    $stmt->close();
    $conn->close();
    exit(1);
}

if ($stmt->affected_rows === 0) {
    $check_query = "SELECT * FROM saje5795_goralys.student_topics WHERE student_id = ? AND topic_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("si", $student_id, $topic_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        GoralysUtility::showToast(
            'error',
            "Soumission",
            "Une erreur interne est survenue lors de la soumission du sujet. Veuillez réessayer ultérieurement",
            js: true
        );
        http_response_code(418); // If you're not a valid student, then you're a teapot
        $check_stmt->close();
        $stmt->close();
        $conn->close();
        exit(1);
    }

    GoralysUtility::showToast(
        'error',
        "Soumission",
        "Une erreur interne est survenue lors de la soumission du sujet. Veuillez réessayer ultérieurement",
        js: true
    );
    http_response_code(500); // Internal server error

    if ($check_stmt) {
        $check_stmt->close();
    }

    $stmt->close();
    $conn->close();
    exit(1);
}

GoralysUtility::showToast(
    'success',
    "Soumission du sujet",
    "Votre sujet a bien été soumis.",
    js: true
);

$stmt->close();
$conn->close();
http_response_code(200); // OK
exit(0);
