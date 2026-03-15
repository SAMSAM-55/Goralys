<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../../src/Kernel/bootstrap.php";

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;


// --------------- Init --------------- //

$kernel = bootKernel();

if (!$kernel->checkAuth("get user role")) {
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
