<?php

namespace Goralys\App\HTTP\Files\Services;

use Goralys\App\HTTP\Files\Interface\FileResponder;
use Goralys\Shared\Exception\Files\InvalidFileException;
use Goralys\Shared\Exception\GoralysRuntimeException;
use JetBrains\PhpStorm\NoReturn;

class HttpFileResponder implements FileResponder
{
    /**
     * @throws GoralysRuntimeException|InvalidFileException
     */
    public function send(string $path, string $name): void
    {
        $realpath = realpath($path);
        if (!$realpath || !is_file($realpath)) {
            throw new InvalidFileException("$path is not a valid file and could not be sent");
        }

        $baseDir = realpath(__DIR__ . "/../../../../../Assets");
        if ($baseDir === false) {
            throw new GoralysRuntimeException("Base directory could not be resolved");
        }

        if (
            !str_starts_with($realpath, $baseDir . DIRECTORY_SEPARATOR)
            && pathinfo($path, PATHINFO_EXTENSION) !== "tmp"
        ) {
            throw new GoralysRuntimeException("Unauthorized file access");
        }

        if (headers_sent($file, $line)) {
            throw new GoralysRuntimeException("Headers were already sent at $file:$line");
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . mime_content_type($realpath) ?: "application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($name) . '"');
        header('Content-Length: ' . filesize($realpath));
        header('X-Content-Type-Options: nosniff');

        readfile($realpath);
    }
}
