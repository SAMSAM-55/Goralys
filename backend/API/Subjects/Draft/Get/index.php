<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;
use Goralys\Shared\Exception\Files\InvalidFileException;
use Goralys\Shared\Exception\GoralysRuntimeException;

require __DIR__ . "/../../../../src/Kernel/bootstrap.php";
require __DIR__ . "/../../../../vendor/autoload.php";

// --------------- Init --------------- //

$kernel = bootKernel(true);

// Custom error messages
$kernel->setExceptionMessage(
    InvalidFileException::class,
    "Le brouillon de l'élève n'a pas pu être trouvé, veuillez réessayer ultérieurement."
);

$request = $kernel->request();
$kernel->requireAuth("download a student draft");
$kernel->requireRole(UserRole::TEACHER, true);

$kernel->run(function (GoralysKernel $kernel, RequestInterface $request) {
    $kernel->requireDb();

    // --------------- Inputs --------------- //

    if (!$request->validate("teacher-token", "student-token", "topic", "file-name")) {
        $kernel->flashToast(
            ToastType::WARNING,
            "Brouillon",
            "Une erreur est survenue lors de la récupération du brouillon de l'élève, 
            veuillez réessayer ultérieurement.",
            "/subject/"
        );
        http_response_code(400); // Bad request
        exit;
    }

    $teacherUsername = $kernel->usernameManager->get($request->get("teacher-token"));
    $studentUsername = $kernel->usernameManager->get($request->get("student-token"));
    $topicName = $request->get("topic");

    $path = $kernel->subjects->draftsManager->getPath($studentUsername, $teacherUsername, $topicName);

    $extension = pathinfo($path, PATHINFO_EXTENSION);
    $fileName = $request->get("file-name");

    $kernel->response()->download($path, $fileName . "." . $extension);
});
