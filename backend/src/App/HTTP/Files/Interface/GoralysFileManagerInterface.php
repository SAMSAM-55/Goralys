<?php

namespace Goralys\App\HTTP\Files\Interface;

use Goralys\App\HTTP\Files\Data\UploadedFileDTO;

interface GoralysFileManagerInterface
{
    public function get(string $fileName): ?UploadedFileDTO;
    public function require(string $fileName): UploadedFileDTO;
    public function all(): array;
    public function move(string $fileName, string $destination): bool;
}
