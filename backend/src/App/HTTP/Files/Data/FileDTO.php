<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Files\Data;

use Goralys\Shared\Exception\GoralysRuntimeException;

readonly class FileDTO
{
    public function __construct(
        public string $name,
        public string $type,
        public string $tmpPath,
        public int $size,
        public int $error
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
