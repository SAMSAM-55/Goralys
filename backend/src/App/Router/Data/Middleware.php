<?php

namespace Goralys\App\Router\Data;

readonly class Middleware
{
    /** @param list<mixed> $params */
    public function __construct(
        public string $name,
        public array $params = []
    ) {
    }
}
