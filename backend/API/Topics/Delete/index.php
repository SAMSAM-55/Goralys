<?php

use Goralys\App\HTTP\Request\GoralysRequest;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;
use Goralys\Shared\Exception\GoralysRuntimeException;

require __DIR__ . "/../../../src/Kernel/bootstrap.php";
require __DIR__ . "/../../../vendor/autoload.php";

// --------------- Init --------------- //

$kernel = bootKernel();
$kernel->getRequest(); // Avoid uninitialized property
$kernel->requireAuth("delete topics");
$kernel->requireRole(UserRole::ADMIN, true);
$kernel->requireCSRF("delete-topics");


$kernel->run(function (GoralysKernel $kernel) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500,
            "Une erreur interne est survenue lors de la connexion, veuillez réessayer ultérieurement.",
            "/subject/"
        );
    }

    $topicsResult = $kernel->topics->clear();
    $usersResult = $kernel->users->clear();

    if (!$topicsResult || !$usersResult) {
        $kernel->toast->fatalError(
            500,
            "Les utilisateurs ou les sujets n'ont pas pu être supprimés, veuillez réessayer ultérieurement.",
            "/subject/"
        );
    }

    $kernel->toast->showToast(
        ToastType::SUCCESS,
        "Suppression des sujets",
        "Tous les sujets et les utilisateurs ont été supprimés (sauf administrateurs).",
        "/subject/"
    );
});
