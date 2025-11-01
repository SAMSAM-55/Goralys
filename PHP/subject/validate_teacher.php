<?php

require_once __DIR__ . '/..' . '/config.php';
require_once __DIR__ . '/..' . '/utility.php';

use Goralys\Config\Config;
use Goralys\Utility\GoralysUtility;

session_start();

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!GoralysUtility::verifyCSRF($data['csrf-token'], true)) {
    die("Invalid validation token");
}

$student_id = isset($data['student-id']) ? trim($data['student-id']) : null;
$topic_id = isset($data['topic-id']) ? (int)$data['topic-id'] : null;

if (!$student_id || !$topic_id) {
    GoralysUtility::showToast(
        'error',
        "Sujet",
        "Une erreur est survenue lors de la validation du sujet. Veuillez réessayer ultérieurement.",
        js: true
    );
    http_response_code(400); // Bad request
    exit();
}

$conn = Config::connectToDatabase();
$query = "UPDATE saje5795_goralys.student_topics SET subject_status = 3 WHERE student_id = ? AND topic_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $student_id, $topic_id);

if (!$stmt->execute()) {
    GoralysUtility::showToast(
        'error',
        "Sujet",
        "Une erreur interne est survenue lors de la validation du sujet.Veuillez réessayer ultérieurement.",
        js: true
    );
    http_response_code(500); // Internal server error
    $stmt->close();
    $conn->close();
    exit(1);
}

if ($stmt->affected_rows === 0) {
    GoralysUtility::showToast(
        'error',
        "Sujet",
        "Une erreur interne est survenue lors de la validation du sujet.Veuillez réessayer ultérieurement.",
        js: true
    );

    $stmt->close();
    $conn->close();
    http_response_code(500); // Internal sever error
    exit(1);
}

GoralysUtility::showToast(
    'success',
    "Sujet",
    "Le sujet a bien été validé",
    js: true
);
$stmt->close();
$conn->close();
http_response_code(200); // OK
exit(0);
