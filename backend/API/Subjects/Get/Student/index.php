<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

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

    $kernel->sendJSON($result);
    exit;
});
