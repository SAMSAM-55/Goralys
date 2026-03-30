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
$kernel->requireRole(UserRole::TEACHER, true);

$kernel->requireAuth("get teacher's subjects");
$kernel->requireCSRF("get-teacher-subjects");

$kernel->run(function (GoralysKernel $kernel) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la récupération de vos questions, 
            veuillez réessayer ultérieurement."
        );
    }

    // ------- Get the subjects ------- //

    $teacherUsername = $_SESSION['current_username'];

    $result = $kernel->subjects->getForRole(
        UserRole::TEACHER,
        $teacherUsername
    );

    $kernel->sendJSON($result);
    exit;
});
