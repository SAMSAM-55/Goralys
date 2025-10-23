<?php
require_once __DIR__ . '/config.php';

// Function to get the full name from the id (format : [firstinitial].[last name][0-9+]
// eg : s.saubion5 -> SAUBION S.
function format_user_id($user_id) {
    // Match: first letter, dot, last name, optional number
    if (preg_match('/^([a-z])\.([a-z]+)\d*$/i', $user_id, $matches)) {
        $first_initial = strtoupper($matches[1]);
        $lastname = strtoupper($matches[2]);
        return "$lastname $first_initial.";
    } else {
        // Return as-is if pattern doesn’t match
        return $user_id;
    }
}

// Function to automatically cache the info for a specified topic id (topic index is either 1 or 2)
function cache_student_topics_info(string $topic_id, int $topic_index)
{
    $conn = connect_to_database();

    $query = "SELECT * FROM saje5795_goralys.topics WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $topic_id);

    if (!$stmt->execute())
        show_toast('error',
        "Erreur",
        "Une erreur interne est survenue, veuillez réessayer ultérieurement"
        );

    $row = $stmt->get_result()->fetch_assoc();

    $_SESSION["user-topic-id-$topic_index"] = $topic_id;
    $_SESSION["user-topic-$topic_index"] = $row['name'];
    $_SESSION["user-teacher-$topic_index"] = format_user_id($row['teacher_id']);

    $stmt->close();
    $conn->close();
}
