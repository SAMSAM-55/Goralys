<?php
require_once __DIR__ . '/config.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

$id = $_GET['user-id'];
$token = $_GET['token'];
$conn = connect_to_database();
$req = "SELECT * FROM saje5795_goralys.users_temp WHERE user_id = ?";
$del = "DELETE FROM saje5795_goralys.users_temp WHERE user_id = ?";
$stmt = $conn->prepare($req);
$stmt->bind_param("s", $id);
$del_stmt = $conn->prepare($del);
$del_stmt->bind_param("s", $id);

if (!$stmt->execute()) {
    http_response_code(403);
    $del_stmt->execute();
    $del_stmt->close();
    $conn->close();
    $stmt->close();

    show_toast('error',
        "Création du compte",
        "Ce lien n'existe pas, veuillez réessayer",
        "register.html");
    exit(1);
}

$result = $stmt->get_result();

if ($result->num_rows === 0 || !($row = $result->fetch_assoc())) {
    http_response_code(400);
    $del_stmt->execute();
    $del_stmt->close();
    $conn->close();
    $stmt->close();

    show_toast('error',
    "Création du compte",
    "ce lien n'existe pas, veuillez recommencer.",
    "register.html");

    exit(1);
}

$validation_token = $row["verification_token"];

if ($validation_token !== $token || $validation_token === "" || ((new DateTime())->getTimestamp() - (new DateTime($row['created_at']))->getTimestamp()) > 750) { // Verify that the token is valid and that the link was sent less than 15 minutes ago.
    http_response_code(400);
    $del_stmt->execute();
    $del_stmt->close();
    $conn->close();
    $stmt->close();

    show_toast('error',
    "création du compte",
    "Le lien a expiré, veuillez recommencer.",
    "register.html");

    exit(1);
}

$password_hash = $row["password_hash"];
$user_name = $row["full_name"];
$user_email = $row['email'];
$user_role = $row['role'];

$create_account_req = "INSERT INTO saje5795_goralys.users (user_id, full_name, email, password_hash, role) VALUES(?, ?, ?, ?, ?)";
$stmt = $conn->prepare($create_account_req);
$stmt->bind_param("sssss", $id, $user_name, $user_email, $password_hash, $user_role);

if ($stmt->execute()) {
    http_response_code(200);
    $del_stmt->execute();
    $del_stmt->close();
    $stmt->close();
    $conn->close();

    show_toast('success',
    "Création du compte",
    "Votre compte chez Goralys a bien été créé. Vous pouvez désormais vous connecter",
    "login.html");

    exit(0);

} else {
    http_response_code(403);
    $del_stmt->execute();
    $del_stmt->close();
    $stmt->close();
    $conn->close();

    show_toast('error',
        "Création du compte",
        "Une erreur est survenue lors de la création de votre compte, veuillez recommancer.",
        "register.html");

    exit(1);
}
