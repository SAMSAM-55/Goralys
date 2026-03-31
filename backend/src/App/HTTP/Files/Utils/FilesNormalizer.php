<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Files\Utils;

use Goralys\App\HTTP\Files\Data\FileDTO;

final class FilesNormalizer
{
    /**
     * @return FileDTO[]
     */
    public static function fromGlobals(array $files): array
    {
        $dtos = [];

        foreach ($files as $inputName => $file) {
            // single file
            if (!is_array($file['name'])) {
                $dtos[$inputName] = new FileDTO(
                    $file['name'],
                    $file['type'],
                    $file['tmp_name'],
                    $file['size'],
                    $file['error'],
                );
                continue;
            }

            // multiple files
            $dtos[$inputName] = [];
            foreach ($file['name'] as $i => $name) {
                $dtos[$inputName][] = new FileDTO(
                    $name,
                    $file['type'][$i],
                    $file['tmp_name'][$i],
                    $file['size'][$i],
                    $file['error'][$i],
                );
            }
        }

        return $dtos;
    }
}
