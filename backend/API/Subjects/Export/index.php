<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

require __DIR__ . "/../../../vendor/autoload.php";
require __DIR__ . "/../../../src/Kernel/bootstrap.php";

use Goralys\Kernel\GoralysKernel;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Shared\Exception\GoralysRuntimeException;

// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->request();
$kernel->requireRole(UserRole::ADMIN, true);

$kernel->requireAuth("export subjects");
$kernel->requireCSRF("export-subjects");

$kernel->run(function (GoralysKernel $kernel) {
    $kernel->requireDb();

    // ------- Export the subjects ------- //

    $kernel->subjects->cleanExports(); // Cleans all previous exports

    $subjects = $kernel->subjects->getForRole(UserRole::ADMIN);
    $path = $kernel->subjects->exportAll($subjects);

    $kernel->response()->download($path, "sujets-go.zip", after: fn() => $kernel->subjects->cleanExports());
});
