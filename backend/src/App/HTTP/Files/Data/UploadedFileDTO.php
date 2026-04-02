<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Files\Data;

readonly class UploadedFileDTO
{
    public function __construct(
        public string $name,
        public string $type,
        public string $tmpPath,
        public int $size
    ) {
    }
}
