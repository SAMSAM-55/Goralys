<?php

use Goralys\App\HTTP\Request\GoralysRequest;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;

require __DIR__ . "/../../../src/Kernel/bootstrap.php";
require __DIR__ . "/../../../vendor/autoload.php";

// --------------- Init --------------- //

$kernel = bootKernel();
$request = $kernel->getRequest();
$kernel->requireAuth("import topics");
$kernel->requireRole(UserRole::ADMIN, true);
$kernel->requireCSRF("import-topics");

$kernel->run(function (GoralysKernel $kernel, GoralysRequest $request) {
    if (!$kernel->connect()) {
        $kernel->toast->fatalError(
            "Une erreur interne est survenue lors de la connexion, veuillez réessayer ultérieurement.",
            "/subject/"
        );
    }

    $archive = $kernel->fileManager->get("topics-file");
    $topics = $kernel->topics->makeTopicsFromZip($archive);

    $kernel->logger->debug(\Goralys\Platform\Logger\Data\Enums\LoggerInitiator::APP, print_r($topics, true));

    foreach ($topics as $topic) {
        $kernel->topics->insert($topic);
    }

    $kernel->toast->showToast(
        ToastType::SUCCESS,
        "Import des données",
        "Les données ont été importées avec succès.",
        "/subject/"
    );
});
