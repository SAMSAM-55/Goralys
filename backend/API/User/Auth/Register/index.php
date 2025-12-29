<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\HTTP\Request\GoralysRequest;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\Kernel\GoralysKernel;

// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->getRequest();
$kernel->requireCSRF("register");

$kernel->run(function (GoralysKernel $kernel, GoralysRequest $request) {
    if (!$kernel->connect()) {
        $kernel->flashFatalError(
            "Une erreur interne est survenue lors de la création du compte, veuillez réessayer ultérieurement.",
            "/user/register"
        );
    }

    // --------------- Inputs --------------- //

    if (!$request->validate("username", "password", "first-name", "last-name")) {
        $kernel->flashtoast(
            ToastType::WARNING,
            "Formulaire",
            "Veuillez remplir tous les champs.",
            "/user/login"
        );
    }

    $username = $request->get("user-name");
    $password = $request->get("password");
    $fullName = $request->get("first-name") . " " . $request->get("last-name");

    // --------------- Register --------------- //

    $registerData = new UserRegisterDTO(
        $username,
        $fullName,
        $password,
    );

    if (!$kernel->auth->register($registerData)) {
        $kernel->flashFatalError(
            "Une erreur interne est survenue lors de la création du compte, veuillez réessayer ultérieurement.",
            "/user/register"
        );
    }

    $kernel->flashToast(
        ToastType::SUCCESS,
        "Création du compte",
        "Votre compte chez Goralys a bien été créé. Vous pouvez maintenant vous connecter.",
        "/user/login"
    );
    exit;
});
