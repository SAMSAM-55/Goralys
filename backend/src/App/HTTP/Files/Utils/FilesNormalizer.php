<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Files\Utils;

use Goralys\App\HTTP\Files\Data\FileDTO;

/**
 * Utility class for normalizing the PHP `$_FILES` superglobal into a flat array of FileDTOs.
 * Handles both single-file and multi-file inputs transparently.
 */
final class FilesNormalizer
{
    /**
     * Converts the raw `$_FILES` superglobal into a map of input name => FileDTO (or FileDTO[]).
     * @param array $files The raw `$_FILES` array.
     * @return FileDTO[] The normalized files array.
     */
    public static function fromGlobals(array $files): array
    {
        $normalized = [];

        foreach ($files as $inputName => $file) {
            // single file
            if (!is_array($file['name'])) {
                $normalized[$inputName] = new FileDTO(
                    $file['name'],
                    $file['type'],
                    $file['tmp_name'],
                    $file['size'],
                    $file['error'],
                );
                continue;
            }

            // multiple files
            $normalized[$inputName] = [];
            foreach ($file['name'] as $i => $name) {
                $normalized[$inputName][] = new FileDTO(
                    $name,
                    $file['type'][$i],
                    $file['tmp_name'][$i],
                    $file['size'][$i],
                    $file['error'][$i],
                );
            }
        }

        return $normalized;
    }
}
