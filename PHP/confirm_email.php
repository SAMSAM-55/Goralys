<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/utility.php';

use Goralys\Config\Config;
use Goralys\Utility\GoralysUtility;

Config::init();

$id = $_GET['user-id'];
$token = $_GET['token'];
$conn = Config::connectToDatabase();
$req = "SELECT * FROM saje5795_goralys.users_temp WHERE user_id = ?";
$del = "DELETE FROM saje5795_goralys.users_temp WHERE user_id = ?";
$stmt = $conn->prepare($req);
$stmt->bind_param("s", $id);
$delStmt = $conn->prepare($del);
$delStmt->bind_param("s", $id);

if (!$stmt->execute()) {
    http_response_code(403);
    $delStmt->execute();
    $delStmt->close();
    $conn->close();
    $stmt->close();

    GoralysUtility::showToast(
        'error',
        "Création du compte",
        "Ce lien n'existe pas, veuillez réessayer",
        "register_page.php"
    );
    exit(1);
}

$result = $stmt->get_result();

if ($result->num_rows === 0 || !($row = $result->fetch_assoc())) {
    http_response_code(400);
    $delStmt->execute();
    $delStmt->close();
    $conn->close();
    $stmt->close();

    GoralysUtility::showToast(
        'error',
        "Création du compte",
        "ce lien n'existe pas, veuillez recommencer.",
        "register_page.php"
    );

    exit(1);
}

$validationToken = $row["verification_token"];

// Verify that the token is valid and that the link was sent less than 15 minutes ago.
if (
    $validationToken !== $token
    || $validationToken === ""
    || ((new DateTime())->getTimestamp() - (new DateTime($row['created_at']))->getTimestamp()) > 750
) {
    http_response_code(400);
    $delStmt->execute();
    $delStmt->close();
    $conn->close();
    $stmt->close();

    GoralysUtility::showToast(
        'error',
        "création du compte",
        "Le lien a expiré, veuillez recommencer.",
        "register_page.php"
    );

    exit(1);
}

$passwordHash = $row["password_hash"];
$userName = $row["full_name"];
$userEmail = $row['email'];
$userRole = $row['role'];

$createAccountRequest = "INSERT INTO saje5795_goralys.users 
    (user_id, full_name, email, password_hash, role) 
    VALUES(?, ?, ?, ?, ?)";
$stmt = $conn->prepare($createAccountRequest);
$stmt->bind_param("sssss", $id, $userName, $userEmail, $passwordHash, $userRole);

if ($stmt->execute()) {
    http_response_code(200);
    $delStmt->execute();
    $delStmt->close();
    $stmt->close();
    $conn->close();

    GoralysUtility::showToast(
        'success',
        "Création du compte",
        "Votre compte chez Goralys a bien été créé. Vous pouvez désormais vous connecter",
        "login_page.php"
    );

    exit(0);
} else {
    http_response_code(403);
    $delStmt->execute();
    $delStmt->close();
    $stmt->close();
    $conn->close();

    GoralysUtility::showToast(
        'error',
        "Création du compte",
        "Une erreur est survenue lors de la création de votre compte, veuillez recommancer.",
        "register_page.php"
    );

    exit(1);
}
