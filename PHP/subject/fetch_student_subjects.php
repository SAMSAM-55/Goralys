<?php

use Goralys\Config\Config;
use Goralys\Utility\GoralysUtility;

session_start();

$student_id = $_SESSION['user-id'] ?? null;

if (!$student_id) {
    GoralysUtility::showToast(
        'error',
        "Sujets",
        "Nous n'avons pas pu retrouver vos sujets",
        "subject-student_page.php"
    );
    http_response_code(500); // Internal server error
    exit(1);
}

$conn = Config::connectToDatabase();
$query = "SELECT * FROM saje5795_goralys.student_topics WHERE student_id = ? LIMIT 2";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    GoralysUtility::showToast(
        'error',
        "Sujets",
        "Nous n'avons pas pu retrouver vos sujets",
        "subject-student_page.php"
    );

    $stmt->close();
    $conn->close();

    http_response_code(500); // Internal server error
    exit(1);
}

$subject_1 = $row;
$row = $result->fetch_assoc();
$subject_2 = $row;

echo json_encode([
    "subject-1" => $subject_1['subject'],
    "subject-1-status" => $subject_1['subject_status'],
    "subject-2" => $subject_2['subject'],
    "subject-2-status" => $subject_2['subject_status'],
]);

$stmt->close();
$conn->close();

http_response_code(200); // OK
exit(0);
