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
$request = $kernel->request();
$kernel->requireRole(UserRole::TEACHER, true);

$kernel->requireAuth("get teacher's subjects");
$kernel->requireCSRF("get-teacher-subjects");

$kernel->run(function (GoralysKernel $kernel) {
    $kernel->requireDb();

    // ------- Get the subjects ------- //

    $result = $kernel->subjects->getForRole(UserRole::TEACHER);

    $kernel->response()->json($result);
});
