<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\Security\CSRF\Services\CSRFService;
use Goralys\App\User\Controllers\AuthController;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Kernel\GoralysKernel;

// --------------- Init --------------- //

$kernel = bootKernel();
// --------------- CSRF --------------- //

$csrfHandler = new CSRFService($kernel->logger);
$token = $csrfHandler->getToken();

if (!$csrfHandler->validate("login", $token)) {
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
            "Une erreur interne est survenue lors de la connexion, veuillez réessayer ultérieurement."
        );
    }

    // --------------- Inputs --------------- //
    $username = trim($_POST['user-name'] ?? "");
    $password = trim($_POST['password-login'] ?? "");

    if (empty($username) || empty($password)) {
        $kernel->toast->showToast(
            ToastType::WARNING,
            "Connexion",
            "Veuillez remplir tous les champs",
            "index.html"
        );
    }

    $userData = new UserLoginDTO(
        $username,
        $password
    );

    // --------------- Login --------------- //
    $auth = new AuthController($kernel->logger, $kernel->db);
    $auth->login($userData);

    http_response_code(200); // OK
    $kernel->toast->showToast(
        ToastType::SUCCESS,
        "Connexion",
        "Vous avez bien été connecté à votre compte.",
        "index.html"
    );
    exit;
});
