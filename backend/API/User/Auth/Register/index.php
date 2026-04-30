<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\Kernel\GoralysKernel;

// --------------- Init --------------- //

$kernel = bootKernel(true);
$request = $kernel->request();
$kernel->requireCSRF("register", "/user/register");

$kernel->run(function (GoralysKernel $kernel, RequestInterface $request) {
    $kernel->requireDb();

    // --------------- Inputs --------------- //

    if (!$request->validate("user-name", "password", "first-name", "last-name")) {
        $kernel->flashToast(
            ToastType::WARNING,
            "Formulaire",
            "Veuillez remplir tous les champs.",
            "/user/login"
        );
        exit;
    }

    $username = $request->get("user-name");
    $password = $request->get("password");
    $fullName = $request->get("first-name") . " " . $request->get("last-name");

    // --------------- Register --------------- //

    $registerData = new UserRegisterDTO(
        $request->get("user-name"),
        $request->get("first-name") . " " . $request->get("last-name"),
        $request->get("password")
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
    $kernel->response()->http();
});
