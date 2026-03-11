<?php

namespace Goralys\App\HTTP\Files\Interface;

use Goralys\App\HTTP\Files\Data\UploadedFileDTO;

/**
 * Interface for file extraction services (e.g., ZIP extractor).
 */
interface FileExtractor
{
    /**
     * Extracts an uploaded file to the specified destination directory.
     *
     * @param UploadedFileDTO $file The uploaded file metadata.
     * @param string $dest The absolute path to the destination directory.
     * @return void
     */
    public function extract(UploadedFileDTO $file, string $dest): void;
}
