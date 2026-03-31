<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

require __DIR__ . "/../../../vendor/autoload.php";
require __DIR__ . "/../../../src/Kernel/bootstrap.php";

use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Kernel\GoralysKernel;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Shared\Exception\GoralysRuntimeException;

// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->getRequest();
$kernel->requireRole(UserRole::ADMIN, true);

$kernel->requireAuth("export subjects");
$kernel->requireCSRF("export-subjects");

$kernel->run(function (GoralysKernel $kernel) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            500, // Internal server error
            "Une erreur interne est survenue lors de la récupération de vos questions, 
            veuillez réessayer ultérieurement."
        );
    }

    // ------- Export the subjects ------- //

    $kernel->subjects->cleanExports(); // Cleans all previous exports

    $subjects = $kernel->subjects->getForRole(UserRole::ADMIN);
    $path = $kernel->subjects->exportAll($subjects);

    if (headers_sent($file, $line)) {
        throw new GoralysRuntimeException("Headers already sent in $file on line $line");
    }

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="sujets-go.zip"');
    header('Content-Length: ' . filesize($path));
    header('Cache-Control: no-cache, must-revalidate');
    header('X-Content-Type-Options: nosniff');

    readfile($path);

    $kernel->subjects->cleanExports();

    exit;
});
