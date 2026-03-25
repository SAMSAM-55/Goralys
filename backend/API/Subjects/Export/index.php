<?php

require __DIR__ . "/../../../vendor/autoload.php";
require __DIR__ . "/../../../src/Kernel/bootstrap.php";

use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Kernel\GoralysKernel;
use Goralys\Core\User\Data\Enums\UserRole;

// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->getRequest();
$kernel->requireRole(UserRole::ADMIN, true);

$kernel->requireAuth("export subjects");
$kernel->requireCSRF("export-subjects");

$kernel->run(function (GoralysKernel $kernel) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la récupération de vos questions, 
            veuillez réessayer ultérieurement."
        );
    }

    // ------- Export the subjects ------- //

    $subjects = $kernel->subjects->getForRole(UserRole::ADMIN);
    $kernel->subjects->exportAll($subjects);

    http_response_code(200); // OK
    $kernel->toast->showToast(
        ToastType::SUCCESS,
        "Export des sujets",
        "Les sujets ont bien été exporté en PDF.",
        "/subject"
    );

    exit;
});
