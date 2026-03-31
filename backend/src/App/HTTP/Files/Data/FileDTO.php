<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Files\Data;

use Goralys\Shared\Exception\GoralysRuntimeException;

class FileDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $tmpPath,
        public readonly int $size,
        public readonly int $error
    ) {
    }

    /**
     * Validates a FileDTO object and turns it into an UploadedFileDTO if the validation pass.
     * @throws GoralysRuntimeException
     */
    public function toUploadedFile(): UploadedFileDTO
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new GoralysRuntimeException("Failed to validate invalid file : " . $this->name);
        }

        return new UploadedFileDTO(
            $this->name,
            $this->type,
            $this->tmpPath,
            $this->size
        );
    }
}
