<?php

namespace Goralys\App\HTTP\Files\Interface;

use Goralys\App\HTTP\Files\Data\UploadedFileDTO;

interface FileExtractor
{
    public function extract(UploadedFileDTO $file, string $dest): void;
}
