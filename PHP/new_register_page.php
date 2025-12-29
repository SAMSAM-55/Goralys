<?php

require __DIR__ . "/vendor/autoload.php";

session_start();

use Goralys\App\Security\CSRF\Services\CSRFService;
use Goralys\Platform\Logger\GoralysLogger;

$logger = new GoralysLogger();

$csrf = new CSRFService($logger);
$csrf->create("register");
$token = $csrf->getForForm("register");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Goralys - Inscription</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="stylesheet" href="./CSS/login-register.css">
    <link rel="stylesheet" href="./CSS/input.css">

    <script type="module" src="./JS/user.js"></script>
    <script type="module" src="JS/header.js"></script>
    <script type="module" src="./JS/toast.js"></script>
    <script type="module" src="./JS/input.js"></script>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer"
    />
</head>
<body>
<!-- Adding the toast notifications elements -->
<div id="toast" class="feedback-container warning" style="display: none;">
    <div class="toast-image">
        <i class="fas"></i>
    </div>
    <div class="toast-content">
    </div>
</div>
<header class="header"></header>
<!-- Main page -->
<main class="main">
    <div class="connection-container">
        <h2>Inscription sur Goralys</h2>
        <form action="../backend/API/User/Auth/Register/index.php" class="connection-form" method="post">
            <input type="hidden" name="csrf-token" id="csrf-token" value="<?= htmlspecialchars($token) ?>">
            <div class="input" id="user-name-container">
                <label for="user-name">Identifiant</label>
                <input type="text" name="user-name" id="user-name" autocomplete="username" required>
                <p class="helper">*Identifiant au format p.nomXX</p>
            </div>
            <div class="input" id="full-name-container">
                <label for="full-name">Nom Complet</label>
                <input type="text" name="full-name" id="full-name" autocomplete="off" required>
                <p class="helper">*De préférence Nom Prénom</p>
            </div>
            <div class="input" id="password-container">
                <label for="password-register">Mot de Passe</label>
                <input type="password" name="password-register" id="password-register" autocomplete="new-password" required>
                <i class="fa-solid fa-eye" id="eye-icon"></i>
            </div>
            <button type="submit" class="submit-button">S'inscrire <i class="fa-solid fa-arrow-right"></i></button>
        </form>
        <p>Vous avez déjà un compte ? <a href="login_page.php">Connectez-vous</a></p>
    </div>
</main>
</body>
</html>