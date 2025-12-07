<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../Kernel/bootstrap.php";

use Goralys\Kernel\GoralysKernel;
use Goralys\App\Security\CSRF\Services\CSRFService;
use Goralys\App\Subjects\Controllers\SubjectsController;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\Enums\UserRole;


// --------------- Init --------------- //

$kernel = bootKernel();

// --------------- CSRF --------------- //

$csrfHandler = new CSRFService($kernel->logger);
$token = $csrfHandler->getToken();

if (!$csrfHandler->validate("get-all-subjects", $token)) {
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

    $subjectsController = new SubjectsController($kernel->logger, $kernel->db);

    $result = $subjectsController->getForRole(UserRole::ADMIN);

    if (!$result) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la récupération de vos questions, 
            veuillez réessayer ultérieurement."
        );
    }

    echo print_r(json_encode($result, JSON_UNESCAPED_UNICODE), true);

    http_response_code(200); // OK
    exit;
});
