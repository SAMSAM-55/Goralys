<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;

require __DIR__ . "/../../../src/Kernel/bootstrap.php";
require __DIR__ . "/../../../vendor/autoload.php";

// --------------- Init --------------- //

$kernel = bootKernel();
$kernel->request(); // Avoid uninitialized property
$kernel->requireAuth("delete topics");
$kernel->requireRole(UserRole::ADMIN, true);
$kernel->requireCSRF("delete-topics");


$kernel->run(function (GoralysKernel $kernel) {
    $kernel->requireDb();

    $kernel->db->beginTransaction();
    $topicsResult = $kernel->topics->clear();
    $usersResult = $kernel->users->clear();

    if (!$topicsResult || !$usersResult) {
        $kernel->db->rollback();
        $kernel->toast->fatalError(
            500,
            "Les utilisateurs ou les sujets n'ont pas pu être supprimés, veuillez réessayer ultérieurement.",
            "/subject/"
        );
    }

    $kernel->db->commit();
    $kernel->toast->showToast(
        ToastType::SUCCESS,
        "Suppression des sujets",
        "Tous les sujets et les utilisateurs ont été supprimés (sauf administrateurs).",
        "/subject/"
    );
    $kernel->response()->http();
});
