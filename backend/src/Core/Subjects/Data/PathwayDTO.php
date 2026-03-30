<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Subjects\Data;

/**
 * DTO used to represent "technological" ways
 */
class PathwayDTO
{
    private string $detectPattern;
    private string $full;

    public function __construct(string $full, string $detectPattern)
    {
        $this->full = $full;
        $this->detectPattern = $detectPattern;
    }

    public function getDetectPattern(): string
    {
        return $this->detectPattern;
    }

    public function getFull(): string
    {
        return $this->full;
    }
}
