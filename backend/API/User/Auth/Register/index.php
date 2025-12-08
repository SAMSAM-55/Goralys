<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\Security\CSRF\Services\CSRFService;
use Goralys\App\User\Controllers\AuthController;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\Kernel\GoralysKernel;

// --------------- Init --------------- //
$kernel = bootKernel();

// --------------- CSRF --------------- //
$csrfHandler = new CSRFService($kernel->logger);
$token = $csrfHandler->getToken();

if (!$csrfHandler->validate("register", $token)) {
    http_response_code(403);
    $kernel->toast->showToast(
        ToastType::WARNING,
        "Lien externe",
        "Ce lien semble inconnu. Ne faite pas confiance aux sources externes.",
        "index.html"
    );
    exit;
}

$kernel->run(function (GoralysKernel $kernel) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la création du compte, veuillez réessayer ultérieurement."
        );
    }

    // --------------- Inputs --------------- //

    $username = trim($_POST['user-name'] ?? "");
    $password = trim($_POST['password-register'] ?? "");
    $fullName = trim($_POST['full-name'] ?? "");

    // --------------- Register --------------- //

    $registerData = new UserRegisterDTO(
        $username,
        $fullName,
        $password,
    );

    $registerController = new AuthController($kernel->logger, $kernel->db);
    if (!$registerController->register($registerData)) {
        $kernel->toast->fatalError(
            403, // Forbidden
            "Une erreur interne est survenue lors de la création du compte, veuillez réessayer ultérieurement."
        );
    }

    http_response_code(200); // OK
    $kernel->toast->showToast(
        ToastType::SUCCESS,
        "Création du compte",
        "Votre compte chez Goralys a bien été créé !",
        "index.html"
    );
    exit;
});
