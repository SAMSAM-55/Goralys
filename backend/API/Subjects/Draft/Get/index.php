<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;
use Goralys\Shared\Exception\GoralysRuntimeException;

require __DIR__ . "/../../../../src/Kernel/bootstrap.php";
require __DIR__ . "/../../../../vendor/autoload.php";

// --------------- Init --------------- //

$kernel = bootKernel(true);
$request = $kernel->request();
$kernel->requireAuth("download a student draft");
$kernel->requireRole(UserRole::TEACHER, true);

$kernel->run(function (GoralysKernel $kernel, RequestInterface $request) {
    if (!$kernel->connect()) {
        $kernel->flashFatalError(
            "Une erreur interne est survenue lors de la connexion, veuillez réessayer ultérieurement.",
            "/subject/"
        );
    }

    // --------------- Inputs --------------- //

    if (!$request->validate("teacher-token", "student-token", "topic", "file-name")) {
        $kernel->flashToast(
            ToastType::WARNING,
            "Brouillon",
            "Une erreur est survenue lors de la récupération du brouillon de l'élève, 
            veuillez réessayer ulérieurement.",
            "/subject/"
        );
        http_response_code(400); // Bad request
        exit;
    }

    $teacherUsername = $kernel->usernameManager->get($request->get("teacher-token"));
    $studentUsername = $kernel->usernameManager->get($request->get("student-token"));
    $topicName = $request->get("topic");

    if (!$path = $kernel->subjects->draftsManager->getPath($studentUsername, $teacherUsername, $topicName)) {
        $kernel->flashFatalError(
            "Une erreur est survenue lors de la récupération du brouillon de l'élève, 
            veuillez réessayer ulérieurement.",
            "/subject/"
        );
    }

    $extension = pathinfo($path, PATHINFO_EXTENSION);
    $fileName = $request->get("file-name");

    $kernel->response()->download($path, $fileName . "." . $extension);
});
