<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Files\Interface;

/**
 * Interface for file move-operations services.
 */
interface FileMover
{
    public ?array $files {
        get;
    }

    /**
     * Moves the specified file a given path.
     * @param string $from The original (current) location of the file.
     * @param string $destination The path of the destination folder.
     * @return bool If the operation is successful or not.
     */
    public function move(string $from, string $destination): bool;
}
