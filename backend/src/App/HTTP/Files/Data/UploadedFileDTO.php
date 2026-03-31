<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

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
