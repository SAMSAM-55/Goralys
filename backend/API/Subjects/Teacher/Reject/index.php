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
use Goralys\Core\Subjects\Data\Enums\SubjectStatus;


// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->request();

$kernel->requireAuth("reject subject");
$kernel->requireRole(UserRole::TEACHER, true);

$kernel->requireCSRF("reject-subject");
$kernel->run(function (GoralysKernel $kernel, RequestInterface $request) {
    $kernel->requireDb();

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
        $kernel->toast->showToast(
            ToastType::INFO,
            "Invalidation",
            "Cette question est déjà invalidée",
            ""
        );
        $kernel->response()->http();
    }

    if ($currentStatus !== SubjectStatus::SUBMITTED) {
        $kernel->toast->fatalError(
            409, // Conflict
            "Vous ne pouvez pas rejeter cette question"
        );
    }

    $kernel->db->beginTransaction();

    $commentResult = $kernel->subjects->updateField(
        $teacherUsername,
        $studentUsername,
        $topic,
        SubjectFields::COMMENT,
        $comment
    );

    if (!$commentResult) {
        $kernel->db->rollback();
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
        $kernel->db->rollback();
        $kernel->toast->fatalError(
            500, // Internal server error
            "La question n'a pas pu être invalidée"
        );
    }

    $kernel->db->commit();
    $kernel->toast->showToast(
        ToastType::INFO,
        "Invalidation",
        "La question a bien été invalidée.",
        ""
    );
    $kernel->response()->http();
});
