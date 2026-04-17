<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\DB\Interfaces;

use mysqli_result;

interface DbContainerInterface
{
    /* Transactions */
    public function beginTransaction(): void;
    public function rollback(): void;
    public function commit(): void;

    public function connect(): bool;
    public function fetch(string $query, string $types, mixed $value1, ...$args): mysqli_result;
    public function fetchNoArgs(string $query): mysqli_result;
    public function run(string $query, string $types, mixed $value1, ...$args): bool;
    public function runNoArgs(string $query): bool;
}
