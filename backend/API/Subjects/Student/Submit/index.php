<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\HTTP\Request\GoralysRequest;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;
use Goralys\App\Subjects\Data\Enums\SubjectFields;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\Subject\Data\Enums\SubjectStatus;


// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->getRequest();

$kernel->requireAuth("submit student subject");
$kernel->requireRole(UserRole::STUDENT, true);
$kernel->requireCSRF("submit-subject");

$kernel->run(function (GoralysKernel $kernel, GoralysRequest $request) {
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
            404,
            "Une erreur interne est survenue lors de l'enregistrement de votre question, 
            veuillez réessayer ultérieurement."
        );
    }

    $teacherUsername = $kernel->usernameManager->get($request->get('teacher-token'));
    $studentUsername = $kernel->usernameManager->get($request->get('student-token'));
    $topic = $request->get('topic');
    $subject = $request->get('subject');

    if (empty($subject) || trim($subject) === "") {
        $kernel->toast->fatalError(
            404, // Bad request
            "Une erreur interne est survenue lors de l'enregistrement de votre question, 
            veuillez réessayer ultérieurement."
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

    // Subject update

    $subjectResult = $kernel->subjects->updateField(
        $teacherUsername,
        $studentUsername,
        $topic,
        SubjectFields::SUBJECT,
        $subject
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
        $kernel->toast->fatalError(
            409, // Conflict
            "Votre sujet a bien été enregistré mais il n'a pas pu être envoyé."
        );
    }

    $kernel->toast->showToast(
        ToastType::INFO,
        "Question",
        "Votre question a bien été envoyée.",
        ""
    );

    http_response_code(200); // OK
    exit;
});
