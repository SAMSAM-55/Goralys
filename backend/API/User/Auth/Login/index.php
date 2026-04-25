<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Kernel\GoralysKernel;

// --------------- Init --------------- //

$kernel = bootKernel(true);
$kernel->requireRateLimit("login", "/user/login", "Trop de tentatives de connexion. Réessayez dans quelques minutes.");
$request = $kernel->request();
$kernel->requireCSRF("login", "/user/login");

$kernel->run(function (GoralysKernel $kernel, RequestInterface $request) {
    $kernel->requireDb();

    // --------------- Inputs --------------- //

    if (!$request->validate("username", "password")) {
        $kernel->flashToast(
            ToastType::WARNING,
            "Formulaire",
            "Veuillez remplir tous les champs.",
            "/user/login"
        );
        exit;
    }

    $username = $request->get("username");
    $password = $request->get("password");

    // Double-check inputs
    if (empty($username) || empty($password)) {
        $kernel->flashToast(
            ToastType::WARNING,
            "Connexion",
            "Veuillez remplir tous les champs",
            "/user/login"
        );
        exit;
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
    $kernel->response()->http();
});
