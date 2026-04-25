<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;


// --------------- Init --------------- //

$kernel = bootKernel();

if (!$kernel->checkAuth()) {
    unset($_SESSION["current_username"]);
    unset($_SESSION["current_role"]);
    unset($_SESSION["current_id"]);
    unset($_SESSION["current_full_name"]);
    exit;
}

// --------------- Build User Data --------------- //
$kernel->logger->info(
    LoggerInitiator::APP,
    "Accessed data of user: " . $_SESSION["current_username"]
);

// --------------- Response --------------- //

$kernel->response()->json(
    [
        "success" => true,
        "role" => trim($_SESSION["current_role"])
    ]
);
