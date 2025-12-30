<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;


// --------------- Init --------------- //

$kernel = bootKernel();

$kernel->requireAuth("get user role");

// --------------- Build User Data --------------- //
$kernel->logger->info(
    LoggerInitiator::APP,
    "Accessed data of user: " . $_SESSION["current_username"]
);

// --------------- Response --------------- //

header("Content-Type: application/json; charset=utf-8");
http_response_code(200);
echo json_encode(
    [
        "success" => true,
        "role" => trim($_SESSION["current_role"])
    ],
    JSON_UNESCAPED_UNICODE
);
exit;
