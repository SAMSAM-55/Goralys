<?php

namespace Goralys\App\HTTP\Files\Interface;

interface FileMover
{
    public function move(string $from, string $destination): bool;
}
