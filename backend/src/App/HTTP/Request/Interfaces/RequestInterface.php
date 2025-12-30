<?php

namespace Goralys\App\HTTP\Request\Interfaces;

interface RequestInterface
{
    public function get(string $key): mixed;
    public function validate(string $key1, string ...$_): bool;
}
