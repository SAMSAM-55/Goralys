<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Shared\Exception\GoralysRuntimeException;

require __DIR__ . "/../../../src/Kernel/bootstrap.php";
require __DIR__ . "/../../../vendor/autoload.php";

// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->request();
$kernel->requireAuth("import topics");
$kernel->requireRole(UserRole::ADMIN, true);
$kernel->requireCSRF("import-topics");

$kernel->run(function (GoralysKernel $kernel) {
    $kernel->requireDb();

    $archive = $kernel->fileManager->get("topics-file");
    $topics = $kernel->topics->makeTopicsFromZip($archive);

    foreach ($topics as $topic) {
        if (!$kernel->topics->insert($topic)) {
            $kernel->db->rollback();
            $kernel->toast->fatalError(
                500, // Internal Server Error
                "Une erreur interne est survenue lors de l'insertion des sujets.",
                "/subject",
            );
        }
    }

    $kernel->db->commit();

    $usernamesFilePath = $kernel->topics->exportUsernames($topics);

    $kernel->response()->download($usernamesFilePath, "utilisateurs.txt");
});
