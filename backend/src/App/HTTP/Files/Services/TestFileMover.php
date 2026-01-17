<?php

namespace Goralys\App\HTTP\Files\Services;

use Goralys\App\HTTP\Files\Interface\FileMover;
use Goralys\Shared\Exception\GoralysRuntimeException;

class TestFileMover implements FileMover
{
    /**
     * @param string $from The original path of the file.
     * @param string $destination The destination of the file.
     * @return bool If the move was successful or not.
     * @throws GoralysRuntimeException If the file is invalid.
     */
    public function move(string $from, string $destination): bool
    {
        if (!is_file($from)) {
            throw new GoralysRuntimeException("Source file does not exist: " . $from);
        }

        $dir = dirname($destination);
        if (!is_dir($dir)) {
            throw new GoralysRuntimeException("Trying to move file to invalid directory: " . $dir);
        }

        if (!rename($from, $destination)) {
            throw new GoralysRuntimeException(
                sprintf('Failed to move file from "%s" to "%s"', $from, $destination)
            );
        }

        return true;
    }
}
