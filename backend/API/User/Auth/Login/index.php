<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\HTTP\Request\GoralysRequest;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Kernel\GoralysKernel;

// --------------- Init --------------- //

$kernel = bootKernel(true);
$request = $kernel->getRequest();
$kernel->requireCSRF("login", "/user/login");

$kernel->run(function (GoralysKernel $kernel, GoralysRequest $request) {
    if (!$kernel->connect()) {
        $kernel->flashFatalError(
            "Une erreur interne est survenue lors de la connexion, veuillez réessayer ultérieurement.",
            "/user/login"
        );
    }

    // --------------- Inputs --------------- //

    if (!$request->validate("username", "password")) {
        $kernel->flashtoast(
            ToastType::WARNING,
            "Formulaire",
            "Veuillez remplir tous les champs.",
            "/user/login"
        );
    }

    $username = $request->get("username");
    $password = $request->get("password");

    if (empty($username) || empty($password)) {
        $kernel->flashToast(
            ToastType::WARNING,
            "Connexion",
            "Veuillez remplir tous les champs",
            "/user/login"
        );
    }

    $userData = new UserLoginDTO(
        $username,
        $password
    );

    // --------------- Login --------------- //

    if (!$kernel->auth->login($userData)) {
        $kernel->flashToast(
            ToastType::ERROR,
            "Connexion",
            "Mot de passe ou identifiant incorrect.",
            "/user/login"
        );
        exit;
    }

    $kernel->flashToast(
        ToastType::SUCCESS,
        "Connexion",
        "Vous avez bien été connecté à votre compte.",
        "/subject/",
        "login-success"
    );
    exit;
});
