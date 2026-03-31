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

$kernel->requireAuth("get user profile");

// --------------- Build User Data --------------- //

$data = [
    "username"   => trim($_SESSION["current_username"]),
    "full_name"  => trim($_SESSION["current_full_name"]),
    "role"       => trim($_SESSION["current_role"])
];

$kernel->logger->info(
    LoggerInitiator::APP,
    "Accessed data of user: " . $data["username"]
);

// --------------- Response --------------- //

header("Content-Type: application/json; charset=utf-8");
http_response_code(200);
echo json_encode(
    [
    "success" => true,
    "data" => $data
    ],
    JSON_UNESCAPED_UNICODE
);
exit;
