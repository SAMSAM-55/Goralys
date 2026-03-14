<?php

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

$kernel->requireAuth("approve subject");
$kernel->requireRole(UserRole::TEACHER);
$kernel->requireCSRF("approve-subject");

$kernel->run(function (GoralysKernel $kernel, GoralysRequest $request) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la validation de la question, veuillez réessayer ultérieurement."
        );
    }

    // --------------- Inputs --------------- //

    if (!$request->validate("topic", "teacher-token", "student-token")) {
        $kernel->toast->fatalError(
            404,
            "Une erreur interne est survenue lors de l'invalidation de la question, veuillez réessayer ultérieurement."
        );
    }

    $teacherUsername = $kernel->usernameManager->get($request->get('teacher-token'));
    $studentUsername = $kernel->usernameManager->get($request->get('student-token'));
    $topic = $request->get("topic");

    $result = $kernel->subjects->updateField(
        $teacherUsername,
        $studentUsername,
        $topic,
        SubjectFields::STATUS,
        SubjectStatus::APPROVED
    );

    if (!$result) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la validation de la question, veuillez réessayer ultérieurement."
        );
    }

    $kernel->toast->showToast(
        ToastType::INFO,
        "Validation",
        "La question a bien été validée.",
        ""
    );

    http_response_code(200); // OK
    exit;
});
