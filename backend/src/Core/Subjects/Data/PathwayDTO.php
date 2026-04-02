<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Subjects\Data;

/**
 * DTO used to represent "technological" ways
 */
readonly class PathwayDTO
{
    public function __construct(
        public string $full,
        public string $detectPattern
    ) {
    }
}
