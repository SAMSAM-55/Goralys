<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\DB\Data;

/**
 * The DTO used to transport the data of a statement across the different services and layers.
 * Its use stops as soon as the statement is prepared (it is now a `mysqli_stmt`).
 */
readonly class StmtDto
{
    public array $args;

    public function __construct(
        public string $query,
        public string $types,
        mixed $value1,
        mixed ...$_
    ) {
        $this->args = [$value1, ...$_];
    }
}
