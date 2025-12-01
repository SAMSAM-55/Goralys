<?php

namespace Goralys\Platform\DB\Interfaces;

use mysqli_stmt;
use mysqli_result;

interface DbContainerInterface
{
    public function connect(): bool;
    public function fetch(string $query, string $types, mixed ...$args): mysqli_result;

    public function run(string $query, string $types, mixed ...$args): bool;
}
