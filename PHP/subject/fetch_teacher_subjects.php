<?php

// This file is used to return the list of subjects associated to a teacher

require_once __DIR__ . '/..' . '/config.php';
require_once __DIR__ . '/..' . '/utility.php';

use Goralys\Config\Config;
use Goralys\Utility\GoralysUtility;

session_start();

$teacher_id = $_SESSION['user-id'] ?? null;

if (!$teacher_id) {
    http_response_code(401); // Unauthorized
    GoralysUtility::showToast(
        'error',
        "Sujets",
        "Nos nous avons pas pu retrouver vos sujets",
        js: true
    );
    exit(1);
}

$conn = Config::connectToDatabase();
$query = "SELECT * FROM saje5795_goralys.topics WHERE teacher_id = ?";
if (!$stmt = $conn->prepare($query)) {
    http_response_code(500); // Internal server error
    GoralysUtility::showToast(
        'error',
        "Sujets",
        "Une erreur interne est survenue lors de la récupération de vos sujets. Veuillez réessayez ultérieurement",
        js: true
    );
    $conn->close();
    $stmt->close();
    exit(1);
}

$stmt->bind_param("s", $teacher_id);

if (!$stmt->execute()) {
    http_response_code(500); // Internal server error
    GoralysUtility::showToast(
        'error',
        "Sujets",
        "Une erreur interne est survenue lors de la récupération de vos sujets. Veuillez réessayez ultérieurement",
        js: true
    );
    $conn->close();
    $stmt->close();
    exit(1);
}

$result = $stmt->get_result();
$subjects_table = [];

while ($row = $result->fetch_assoc()) {
    $topic_id = $row['id'];
    $topic_name = $row['name'];

    $subject_query = "SELECT * FROM saje5795_goralys.student_topics WHERE topic_id = ?";
    $subject_stmt = $conn->prepare($subject_query);
    $subject_stmt->bind_param("i", $topic_id);

    if (!$subject_stmt->execute()) {
        http_response_code(500); // Internal server error
        GoralysUtility::showToast(
            'error',
            "Sujets",
            "Une erreur interne est survenue lors de la récupération de vos sujets. Veuillez réessayez ultérieurement",
            js: true
        );
        $conn->close();
        $subject_stmt->close();
        $stmt->close();
        exit(1);
    }

    $subject_result = $subject_stmt->get_result();
    while ($subject_row = $subject_result->fetch_assoc()) {
        $subjects_table[] = [
                "student-name"   => GoralysUtility::formatUserId($subject_row['student_id']),
                "student-id"     => $subject_row['student_id'],
                "topic-name"     => $topic_name,
                "topic-id"       => $subject_row['topic_id'],
                "subject"        => $subject_row['subject_status'] === 2
                                    ? $subject_row['last_rejected'] // Show the last subject that was rejected
                                    : $subject_row['subject'],
                "subject-status" => $subject_row['subject_status']
        ];
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
        "data" => $subjects_table
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
http_response_code(200); // OK
exit(0);
