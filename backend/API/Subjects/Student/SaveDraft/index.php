<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;
use Goralys\App\Subjects\Data\Enums\SubjectFields;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;


// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->request();

$kernel->requireAuth("save student draft");
$kernel->requireRole(UserRole::STUDENT, true);
$kernel->requireCSRF("save-draft");

$kernel->run(function (GoralysKernel $kernel, RequestInterface $request) {
    $kernel->requireDb();

    // --------------- Inputs --------------- //

    if (!$request->validate("draft", "topic", "teacher-token", "student-token")) {
        $kernel->toast->fatalError(
            404,
            "Une erreur interne est survenue lors de l'enregistrement de votre brouillon, 
            veuillez réessayer ultérieurement."
        );
    }

    $teacherUsername = $kernel->usernameManager->get($request->get('teacher-token'));
    $studentUsername = $kernel->usernameManager->get($request->get('student-token'));
    $topic = $request->get('topic');
    $newSubject = $request->get('draft');
    $interdisciplinary = (bool)$request->get('interdisciplinary');

    // As the user must be a student, we can do a quick-validation to ensure the usernam token is correct.

    if ($studentUsername !== $_SESSION['current_username']) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de l'enregistrement de votre brouillon, 
            veuillez réessayer ultérieurement."
        );
    }

    $result = $kernel->subjects->updateField(
        $kernel->usernameManager->get($request->get('teacher-token')),
        $kernel->usernameManager->get($request->get('student-token')),
        $request->get('topic'),
        SubjectFields::SUBJECT,
        $request->get('draft'),
        (bool)$request->get('interdisciplinary')
    );

    if (!$result) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de l'enregistrement de votre brouillon, 
            veuillez réessayer ultérieurement."
        );
    }

    $kernel->toast->showToast(
        ToastType::INFO,
        "Question",
        "Votre brouillon a bien été enregistré.",
        ""
    );

    $kernel->response()->http();
});
