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
$request = $kernel->getRequest();

$kernel->requireAuth("submit student subject");
$kernel->requireRole(UserRole::STUDENT, true);
$kernel->requireCSRF("submit-subject");

$kernel->run(function (GoralysKernel $kernel, RequestInterface $request) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de l'enregistrement de votre question, 
            veuillez réessayer ultérieurement."
        );
    }

    // --------------- Inputs --------------- //

    if (!$request->validate("subject", "topic", "teacher-token", "student-token")) {
        $kernel->toast->fatalError(
            400, // Bad request
            "Veuillez vérifier que tous les champs requis ont été correctement renseignés et que les données sont 
            valides."
        );
    }

    $teacherUsername = $kernel->usernameManager->get($request->get('teacher-token'));
    $studentUsername = $kernel->usernameManager->get($request->get('student-token'));
    $topic = $request->get('topic');
    $subject = $request->get('subject');
    $interdisciplinary = (bool)$request->get('interdisciplinary');
    $draftFile = $kernel->fileManager->get("draft-file");

    if ($draftFile && $draftFile->size > 50 * 1024) {
        $kernel->toast->showToast(
            ToastType::WARNING,
            "Fichier",
            "Ce fichier dépasse la taille maximale de 50 KO, veuillez ressayez avec un fichier plus petit.",
            ""
        );
        exit();
    }

    // Double-check subject.

    if (empty($subject) || trim($subject) === "") {
        $kernel->toast->fatalError(
            400, // Bad request
            "Le champ 'sujet' ne peut pas être laissé vide. Veuillez indiquer un sujet pour votre question."
        );
    }

    // As the user must be a student, we can do a quick-validation to ensure the usernam token is correct.

    if ($studentUsername !== $_SESSION['current_username']) {
        $kernel->toast->fatalError(
            403, // Forbidden
            "Une erreur interne est survenue lors de l'enregistrement de votre question, 
            veuillez réessayer ultérieurement."
        );
    }

    $kernel->db->beginTransaction();
    // Subjects update

    $subjectResult = $kernel->subjects->updateField(
        $teacherUsername,
        $studentUsername,
        $topic,
        SubjectFields::SUBJECT,
        $subject,
        $interdisciplinary
    );

    if (!$subjectResult) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de l'enregistrement de votre question, 
            veuillez réessayer ultérieurement."
        );
    }

    // Status update

    $statusResult = $kernel->subjects->updateField(
        $teacherUsername,
        $studentUsername,
        $topic,
        SubjectFields::STATUS,
        SubjectStatus::SUBMITTED
    );

    if (!$statusResult) {
        $kernel->db->rollback();
        $kernel->toast->fatalError(
            409, // Conflict
            "Votre n'a pas pu être envoyée. Veuillez réessayer ultérieurement."
        );
    }

    if ($draftFile) {
        $updateResult = $kernel->subjects->draftsManager->update($studentUsername, $teacherUsername, $topic);

        if (!$updateResult) {
            $kernel->db->rollback();
            $kernel->toast->fatalError(
                500, // Internal server error
                "Votre question n'a pas pu être envoyée, car votre brouillon n'a pas pu être enregistré. 
                Veuillez réessayer ultérieurement."
            );
        }
    }

    $kernel->db->commit();
    $kernel->toast->showToast(
        ToastType::INFO,
        "Question",
        "Votre question a bien été envoyée.",
        ""
    );

    http_response_code(200); // OK
    exit;
});
