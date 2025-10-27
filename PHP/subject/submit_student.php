<?php
require_once __DIR__ . '/' . '../config.php';
session_start();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

error_log("PHP raw input : " . $raw);
error_log("PHP Received subject index : " . $data['subject-index']);

// Doesn't actually mind the subject status for now
$subject_index = (string)$data['subject-index'] ?? null;
$student_id = $_SESSION['user-id'] ?? null;
$topic_id = $_SESSION['user-topic-id-' . $subject_index] ?? null;

// Variables validation
$subject_index = is_string($subject_index) ? trim($subject_index) : null;
$student_id = is_string($student_id) ? trim($student_id) : null;
$topic_id = is_scalar($topic_id) ? $topic_id : null;

if (!$topic_id || !$student_id || !$subject_index)
{
    show_toast('error',
    "Soumission",
    "Une erreur interne est survenue lors de la soumission du sujet. Veuillez réessayer ultérieurement",
    "subject-student.html",
    js: true);
    http_response_code(400); // Bad request
    exit(1);
}

$conn = connect_to_database();
$query = "UPDATE saje5795_goralys.student_topics SET subject_status = 1 WHERE student_id = ? AND topic_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $student_id, $topic_id);

if (!$stmt->execute())
{
    show_toast('error',
        "Soumission",
        "Une erreur interne est survenue lors de la soumission du sujet. Veuillez réessayer ultérieurement",
        js: true);
    http_response_code(500); // Internal server error
    $stmt->close();
    $conn->close();
    exit(1);
}

if ($stmt->affected_rows === 0)
{
    $check_query = "SELECT * FROM saje5795_goralys.student_topics WHERE student_id = ? AND topic_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("si", $student_id, $topic_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0)
    {
        show_toast('error',
            "Soumission",
            "Une erreur interne est survenue lors de la soumission du sujet. Veuillez réessayer ultérieurement",
            js: true);
        http_response_code(418); // If you're not a valid student, then you're a teapot
        $check_stmt->close();
        $stmt->close();
        $conn->close();
        exit(1);
    }

    show_toast('error',
        "Soumission",
        "Une erreur interne est survenue lors de la soumission du sujet. Veuillez réessayer ultérieurement",
        js: true);
    http_response_code(500); // Internal server error

    if ($check_stmt)
        $check_stmt->close();

    $stmt->close();
    $conn->close();
    exit(1);
}

show_toast('success',
"Soumission du sujet",
"Votre sujet a bien été soumis.",
js: true);

$stmt->close();
$conn->close();
http_response_code(200); // OK
exit(0);
