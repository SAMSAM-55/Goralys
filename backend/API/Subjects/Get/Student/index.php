<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\Kernel\GoralysKernel;
use Goralys\Core\User\Data\Enums\UserRole;


// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->getRequest();

$kernel->requireAuth("get student's subject");
$kernel->requireRole(UserRole::STUDENT);
$kernel->requireCSRF("get-student-subjects");

$kernel->run(function (GoralysKernel $kernel) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la récupération de vos questions, 
            veuillez réessayer ultérieurement."
        );
    }

    // ------- Get the subjects ------- //

    $studentUsername = $_SESSION['current_username'];

    $result = $kernel->subjects->getForRole(
        UserRole::STUDENT,
        $studentUsername
    );

    if (!$result) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la récupération de vos questions, 
            veuillez réessayer ultérieurement."
        );
    }

    $kernel->sendJSON($result);
    exit;
});
