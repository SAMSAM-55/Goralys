<?php

namespace Goralys\App\HTTP\Files\Data;

class UploadedFileDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $tmpPath,
        public readonly int $size
    ) {
    }
}
