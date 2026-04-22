<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Shared\Exception\GoralysRuntimeException;

require __DIR__ . "/../../../vendor/autoload.php";
require __DIR__ . "/../../../src/Kernel/bootstrap.php";

// --------------- Init --------------- //

$kernel = bootKernel();

// -------- Process the toast -------- //

try {
    $kernel->logger->debug(
        LoggerInitiator::APP,
        "Attempting to retrieve the flash toast, current session: " . print_r($_SESSION, true)
    );
    $toast = $kernel->toast->flashService->getToast();
} catch (GoralysRuntimeException) {
    $kernel->response()->json(['success' => false]);
}

$kernel->response()->json([
    "success" => true,
    "toast" => $toast->toastInfo,
    "action" => $toast->action
]);
