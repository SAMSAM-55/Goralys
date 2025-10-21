<?php
require __DIR__ . '/config.php';
global $mail_domain, $mail_password, $mail_user, $folder; // Variables to connect to mail server

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$user_id = $_REQUEST['user-id'];
$email = $_REQUEST['email-register'];
$password = $_REQUEST['password-register'];
$user_name = $_REQUEST['user-name'];

if (empty("$email") || empty("$password") || empty("$user_name") || empty($user_id)) {
    echo "<script type='text/javascript'>
        window.location.href = window.location.origin + '$folder' + '/register.html';
        alert('Veuillez remplir tous les champs.');
    </script>";
    exit();
}

$conn = connect_to_database();
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));
$create_account = "INSERT INTO saje5795_goralys.users_temp (user_id, full_name, email, password_hash, role, verification_token) VALUES('$user_id', '$user_name', '$email', '$hashed_password', 'student', '$token')";

if ($conn -> query("SELECT * FROM saje5795_goralys.users_temp WHERE user_id = '$user_id'")->num_rows > 0) {
    $delete_temp = "DELETE FROM saje5795_goralys.users_temp WHERE user_id = ?";
    $delete_stmt = $conn -> prepare($delete_temp);
    $delete_stmt -> bind_param("s", $user_id);
    $delete_stmt -> execute();
    $delete_stmt->close();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[A-Za-z]+\.[A-Za-z]+\d+$/', $user_id)) {
    $conn->close();
    show_toast('warning',
        "Email ou identifiant incorrect",
        "Veuillez entre un email et un identifiant valides",
        "register.html");
    exit();
}

// Check if the id is valid
$check_id_req = "SELECT user_id FROM 
                   (SELECT student_id AS user_id FROM saje5795_goralys.student_topics 
                    UNION 
                    SELECT teacher_id AS user_id FROM saje5795_goralys.topics) 
                    as all_ids WHERE user_id = ? LIMIT 1";
$check_id_stmt = $conn->prepare($check_id_req);
$check_id_stmt->bind_param("s", $user_id);
$check_id_stmt->execute();
$res = $check_id_stmt->get_result();

if (!$res || $res->num_rows > 0)
{
    show_toast('error',
    "Identifiant invalide",
    "Votre identifiant $user_id n'est pas valide, veuillez réessayer",
    "register.html");
    $conn -> close();
    exit();
}

if ($conn -> query($create_account) === TRUE) {
    $toast_type = 'info';
    $toast_title = 'Création du compte';
    $toast_message = 'Pour finaliser la création de votre compte, veuillez vérifier votre email. Consulter votre boite de réception.';
    $conn->close();

    $mail = new PHPMailer(true);
    $link = "https://" . $_SERVER["HTTP_HOST"] . "$folder/PHP/confirm-email.php?token=" . urlencode($token) . "&user-id=" . urlencode($user_id);

    try {
        // Setup for the email (replace the placeholders values with valid ones)
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->Host = $mail_domain;
        $mail->SMTPAuth = true;
        $mail->Username = $mail_user;
        $mail->Password = $mail_password;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom($mail_user, 'Goralys');
        $mail->addAddress($email);

        $mail->Subject = 'Vérification de votre compte';
        $mail->Body    = "Bonjour $user_name,\nMerci de votre inscription à Goralys. Pour finaliser la création de votre compte veuillez cliquer sur le lien ci-dessous : \n$link\nCe lien expirera dans quinze minutes\n\nMerci de ne pas répondre à cet e-mail.";

        $mail->send();
    } catch (Exception $e) {
        $toast_type = 'error';
        $toast_title = 'Création du compte';
        $toast_message = "Une erreur est lors de l'envoie de l'email de confirmation";
    }

    show_toast($toast_type,
        $toast_title,
        $toast_message);
} else {
    $toast_type = 'error';
    $toast_title = 'Erreur';
    $toast_message = "Votre compte n'a pas pu être créé. Veuillez réessayer.";
    show_toast($toast_type,
        $toast_title,
        $toast_message,
        "register.html");
    $conn -> close();
}
exit();