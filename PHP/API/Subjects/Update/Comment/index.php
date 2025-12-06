<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../API/Kernel/bootstrap.php";

use Goralys\API\Kernel\GoralysKernel;
use Goralys\App\Security\CSRF\Services\CSRFService;
use Goralys\App\Subjects\Controllers\SubjectsController;
use Goralys\App\Subjects\Data\Enums\SubjectFields;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\Subject\Data\Enums\SubjectStatus;


// --------------- Init --------------- //

$kernel = bootKernel();

// --------------- CSRF --------------- //

$csrfHandler = new CSRFService($kernel->logger);
$token = $csrfHandler->getToken();

if (!$csrfHandler->validate("update-subject-status", $token)) {
    http_response_code(403);
    $kernel->toast->showToast(
        ToastType::WARNING,
        "Lien externe",
        "Ce lien semble inconnu. Ne faite pas confiance aux sources externes.",
        "index.html"
    );
    exit;
}

$kernel->run(function (GoralysKernel $kernel) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la récupération de vos questions, 
            veuillez réessayer ultérieurement."
        );
    }

    // --------------- Inputs --------------- //

    $teacherUsername = $kernel->getInputByKey('teacher-username');
    $studentUsername = $kernel->getInputByKey('student-username');
    $newStatus = SubjectStatus::fromString($kernel->getInputByKey('new-status'));

    $subjectsController = new SubjectsController($kernel->logger, $kernel->db);

    $result = $subjectsController->updateField(
        $teacherUsername,
        $studentUsername,
        SubjectFields::COMMENT,
        $newStatus
    );

    if (!$result) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la récupération de vos questions, 
            veuillez réessayer ultérieurement."
        );
    }

    http_response_code(200); // OK
    exit;
});
