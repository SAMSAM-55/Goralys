<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\App\Security\CSRF\Services\CSRFService;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;

// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->request();

// -------- Create the token -------- //

$csrfHandler = new CSRFService($kernel->logger);
$formId = $request->get("form-id");

if (empty($formId)) {
    $kernel->logger->warning(LoggerInitiator::APP, "Unable to get the form id to generate the token");
}

if (!$csrfHandler->create($formId)) {
    http_response_code(500); // Internal server error
    exit;
}

http_response_code(200); // OK
echo json_encode([
    "csrf-token" => $csrfHandler->getForForm($formId)
]);
