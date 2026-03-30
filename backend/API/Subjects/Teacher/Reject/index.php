<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\HTTP\Request\GoralysRequest;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;
use Goralys\App\Subjects\Data\Enums\SubjectFields;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\Subjects\Data\Enums\SubjectStatus;


// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->getRequest();

$kernel->requireAuth("reject subject");
$kernel->requireRole(UserRole::TEACHER, true);

$kernel->requireCSRF("reject-subject");
$kernel->run(function (GoralysKernel $kernel, GoralysRequest $request) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de l'invalidation de la question, veuillez réessayer ultérieurement."
        );
    }

    // --------------- Inputs --------------- //

    if (!$request->validate("comment", "topic", "teacher-token", "student-token")) {
        $kernel->toast->fatalError(
            404,
            "Une erreur interne est survenue lors de l'invalidation de la question, veuillez réessayer ultérieurement."
        );
    }

    $teacherUsername = $kernel->usernameManager->get($request->get('teacher-token'));
    $studentUsername = $kernel->usernameManager->get($request->get('student-token'));
    $topic = $request->get('topic');
    $comment = $request->get('comment');

    if (empty($comment) || trim($comment) === "") {
        $kernel->toast->fatalError(
            403, // Bad request
            "Une erreur interne est survenue lors de l'invalidation de la question, veuillez réessayer ultérieurement."
        );
    }

    $currentStatus = $kernel->subjects->getStatus($teacherUsername, $studentUsername, $topic);
    if ($currentStatus === SubjectStatus::REJECTED) {
        http_response_code(200);
        $kernel->toast->showToast(
            ToastType::INFO,
            "Invalidation",
            "Cette question est déjà invalidée",
            ""
        );
        exit;
    }

    if ($currentStatus !== SubjectStatus::SUBMITTED) {
        $kernel->toast->fatalError(
            409, // Conflict
            "Vous ne pouvez pas rejeter cette question"
        );
    }

    $commentResult = $kernel->subjects->updateField(
        $teacherUsername,
        $studentUsername,
        $topic,
        SubjectFields::COMMENT,
        $comment
    );

    if (!$commentResult) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Impossible d'enregistrer votre commentaire."
        );
    }

    $statusResult = $kernel->subjects->updateField(
        $teacherUsername,
        $studentUsername,
        $topic,
        SubjectFields::STATUS,
        SubjectStatus::REJECTED
    );

    if (!$statusResult) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Votre commentaire a été enregistré mais la question n'a pas pu être invalidée"
        );
    }

    http_response_code(200); // OK
    $kernel->toast->showToast(
        ToastType::INFO,
        "Invalidation",
        "La question a bien été invalidée.",
        ""
    );
    exit;
});
