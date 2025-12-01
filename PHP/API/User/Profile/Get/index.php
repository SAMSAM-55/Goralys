<?php

require __DIR__ . "/../../../../vendor/autoload.php";
require __DIR__ . "/../../../Kernel/bootstrap.php";

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;


// --------------- Init --------------- //
$kernel = bootKernel();

// --------------- Auth Check --------------- //

if (!isset($_SESSION['current_id'])) {
    $kernel->logger->warning(
        LoggerInitiator::APP,
        "Tried to access forbidden user data without authentication."
    );

    http_response_code(403); // Forbidden
    echo json_encode(
        [
            "success" => false,
            "error" => "Not authenticated"
        ]
    );
    exit;
}

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
