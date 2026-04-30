<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Files\Services;

use Goralys\App\HTTP\Files\Data\UploadedFileDTO;
use Goralys\App\HTTP\Files\Interface\FileExtractor;
use Goralys\Shared\Exception\GoralysRuntimeException;
use ZipArchive;

/**
 * The HTTP service used to extract ZIP archives.
 */
final class HttpFileExtractor implements FileExtractor
{
    /**
     * Ensures a given file is a valid ZIP archive.
     * @param UploadedFileDTO $file The file to validate.
     * @return void
     * @throws GoralysRuntimeException If the validation fails.
     */
    private function ensureZip(UploadedFileDTO $file): void
    {
        $path = $file->tmpPath;
        $originalName = $file->name;

        if (!is_file($path)) {
            throw new GoralysRuntimeException(
                "Uploaded file not found at path: $path"
            );
        }

        if (strtolower(pathinfo($originalName, PATHINFO_EXTENSION)) !== 'zip') {
            throw new GoralysRuntimeException(
                "The uploaded file must have a .zip extension."
            );
        }

        $zip = new ZipArchive();
        $result = $zip->open($path);

        if ($result !== true) {
            throw new GoralysRuntimeException(
                "The provided file is not a valid zip archive."
            );
        }

        $zip->close();
    }

    /**
     * Extracts a zip file
     * @param UploadedFileDTO $file The file to extract.
     * @param string $dest The destination folder where the file will be extracted.
     * @return void
     * @throws GoralysRuntimeException Only thrown if the file is not a valid zip or the code could not extract it.
     */
    public function extract(UploadedFileDTO $file, string $dest): void
    {
        $this->ensureZip($file);

        if (!is_dir($dest) && !mkdir($dest, 0777, true)) {
            throw new GoralysRuntimeException("Could not create extraction directory: $dest");
        }

        $zip = new ZipArchive();
        $opened = $zip->open($file->tmpPath);

        if ($opened !== true) {
            throw new GoralysRuntimeException("Unable to open ZIP file: $file->tmpPath (error code: $opened)");
        }

        if (!$zip->extractTo($dest)) {
            $zip->close();
            throw new GoralysRuntimeException("Unable to extract ZIP file to: $dest");
        }

        $zip->close();
    }
}
