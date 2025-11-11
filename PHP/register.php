<?php

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/utility.php';

use Goralys\Config\Config;
use Goralys\Utility\GoralysUtility;

Config::init();

session_start();

if (!GoralysUtility::verifyCSRF()) {
    die("Invalid CSRF Token");
}

$user_id   = isset($_POST['user-id']) ? trim($_POST['user-id']) : null;
$email     = isset($_POST['email-register']) ? trim($_POST['email-register']) : null;
$password  = isset($_POST['password-register']) ? trim($_POST['password-register']) : null;
$user_name = isset($_POST['user-name']) ? trim($_POST['user-name']) : null;

if (empty($email) || empty($password) || empty($user_name) || empty($user_id)) {
    $redirect = Config::FOLDER . '/register_page.php';
    echo "<script type='text/javascript'>
        window.location.href = window.location.origin + '$redirect';
        alert('Veuillez remplir tous les champs.');
    </script>";
    http_response_code(400); // Bad request
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[A-Za-z]+\.[A-Za-z]+\d+$/', $user_id)) {
    GoralysUtility::showToast(
        'warning',
        "Email ou identifiant incorrect",
        "Veuillez entre un email et un identifiant valides",
        "register_page.php"
    );
    http_response_code(400); // Bad request
    exit();
}

$conn = Config::connectToDatabase();
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));
$create_account_stmt = $conn->prepare("INSERT INTO saje5795_goralys.users_temp 
    (user_id, full_name, email, password_hash, role, verification_token) 
    VALUES(?, ?, ?, ?, ?, ?)");

// Check for pending registration and delete if exists
$check_id_temp_stmt = $conn->prepare("SELECT * FROM saje5795_goralys.users_temp WHERE user_id = ?");
$check_id_temp_stmt->bind_param('s', $user_id);

if (!$check_id_temp_stmt->execute()) {
    GoralysUtility::showToast(
        'error',
        "Création du compte",
        "Une erreur interne est survenue lors de la création de votre compte. Veuilez réessayer."
    );
    $check_id_temp_stmt->close();
    $conn->close();
    http_response_code(500); // Internal server error
    exit();
}

if ($check_id_temp_stmt->get_result()->num_rows > 0) {
    $delete_temp = "DELETE FROM saje5795_goralys.users_temp WHERE user_id = ?";
    $delete_stmt = $conn -> prepare($delete_temp);
    $delete_stmt -> bind_param("s", $user_id);
    $delete_stmt -> execute();
    $delete_stmt->close();
    $conn->close();
    http_response_code(500); // Internal server error
    exit();
}

// Check if the id is valid and allows for automatic role assignment
$check_id_req = "
SELECT user_id, source FROM (
    SELECT student_id AS user_id, 'student_topics' AS source
    FROM saje5795_goralys.student_topics
    UNION ALL
    SELECT teacher_id AS user_id, 'topics' AS source
    FROM saje5795_goralys.topics
) AS all_ids
WHERE user_id = ?
LIMIT 1
";
$check_id_stmt = $conn->prepare($check_id_req);
$check_id_stmt->bind_param("s", $user_id);
$check_id_stmt->execute();
$res = $check_id_stmt->get_result();

if (!$res || $res->num_rows == 0) {
    GoralysUtility::showToast(
        'error',
        "Identifiant invalide",
        "Votre identifiant $user_id n'est pas valide, veuillez réessayer",
        "register_page.php"
    );
    $conn -> close();
    exit();
}

$row = $res->fetch_assoc();
$role = $row["source"] == "student_topics" ? "student" : "teacher";
$create_account_stmt->bind_param("ssssss", $user_id, $user_name, $email, $hashed_password, $role, $token);

if ($create_account_stmt -> execute()) {
    $toast_type = 'info';
    $toast_title = 'Création du compte';
    $toast_message = 'Pour finaliser la création de votre compte, veuillez vérifier votre email. 
    Consulter votre boite de réception.';

    $mail = new PHPMailer(true);
    $link = "https://" . $_SERVER["HTTP_HOST"] . Config::FOLDER . "PHP/confirm_email.php"
    . "?token=" . urlencode($token)
    . "&user-id=" . urlencode($user_id);

    try {
        // Setup for the email (replace the placeholders values with valid ones)
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->Host = Config::$MAIL_DOMAIN;
        $mail->SMTPAuth = true;
        $mail->Username = Config::$MAIL_USER;
        $mail->Password = Config::$MAIL_PASSWORD;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom(Config::$MAIL_USER, 'Goralys');
        $mail->addAddress($email);

        $mail->Subject = 'Vérification de votre compte';
        $mail->Body    = "Bonjour $user_name,\n
        Merci de votre inscription à Goralys.\n
        Pour finaliser la création de votre compte veuillez cliquer sur le lien ci-dessous : \n
        $link\n
        Ce lien expirera dans quinze minutes\n\n
        Merci de ne pas répondre à cet e-mail automatique.";

        $mail->send();
        http_response_code(200); // OK
    } catch (Exception $e) {
        $toast_type = 'error';
        $toast_title = 'Création du compte';
        $toast_message = "Une erreur est lors de l'envoie de l'email de confirmation";
        http_response_code(500); // Internal server error
    }

    $conn->close();
    $create_account_stmt->close();

    GoralysUtility::showToast(
        $toast_type,
        $toast_title,
        $toast_message
    );
} else {
    $toast_type = 'error';
    $toast_title = 'Erreur';
    $toast_message = "Votre compte n'a pas pu être créé. Veuillez réessayer.";
    GoralysUtility::showToast(
        $toast_type,
        $toast_title,
        $toast_message,
        "register_page.php"
    );

    $create_account_stmt->close();
    $conn -> close();
    http_response_code(500); // Internal server error
}
exit();
