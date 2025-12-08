<?php

namespace Goralys\Platform\Loader\Interfaces;

interface EnvInterface
{
    public function load(string $path): bool;

    public function getByKey(string $key): mixed;
}
