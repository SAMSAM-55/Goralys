<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Kernel\GoralysKernel;

// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->getRequest();
$kernel->requireCSRF("logout");

$kernel->run(function (GoralysKernel $kernel) {

    // --------------- Logout --------------- //

    $kernel->auth->logout();

    http_response_code(200); // OK
    $kernel->toast->showToast(
        ToastType::SUCCESS,
        "Connexion",
        "Vous avez bien été déconnecté.",
        "index.html"
    );
    exit;
});
