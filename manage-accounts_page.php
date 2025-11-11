<?php
// Empty for now
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Goralys - Gestion des Comptes</title>
    <link rel="stylesheet" href="./CSS/style.css">

    <script type="module" src="./JS/user.js"></script>
    <script type="module" src="JS/header.js"></script>
    <script type="module" src="./JS/toast.js"></script>

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
<!-- Main page
Only for admins, not implemented yet
-->
<main class="main">
    <h1>Page de gestion des comptes pour administrateurs</h1>
</main>
</body>
</html>
