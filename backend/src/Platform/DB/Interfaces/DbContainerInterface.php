<?php

namespace Goralys\Platform\DB\Interfaces;

use mysqli_stmt;
use mysqli_result;

interface DbContainerInterface
{
    public function connect(): bool;
    public function fetch(string $query, string $types, mixed $value1, ...$args): mysqli_result;
    public function fetchNoArgs(string $query): mysqli_result;
    public function run(string $query, string $types, mixed $value1, ...$args): bool;
    public function runNoArgs(string $query): bool;
}
