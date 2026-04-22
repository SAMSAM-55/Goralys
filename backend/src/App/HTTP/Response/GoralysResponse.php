<?php

namespace Goralys\App\HTTP\Response;

use Goralys\App\HTTP\Files\Interface\FileResponder;
use Goralys\App\HTTP\Response\Interfaces\ResponseInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use JetBrains\PhpStorm\NoReturn;
use Throwable;

class GoralysResponse implements ResponseInterface
{
    public FileResponder $files;
    public LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, FileResponder $files)
    {
        $this->files = $files;
        $this->logger = $logger;
    }

    #[NoReturn]
    public function download(string $path, string $name): void
    {
        try {
            $this->files->send($path, $name);
            $this->logger->info(LoggerInitiator::APP, "File $path was successfully downloaded");
            exit;
        } catch (Throwable $e) {
            $this->logger->error(LoggerInitiator::APP, "Failed to download file: $path\n Error: " . $e->getMessage());
            exit;
        }
    }
}
