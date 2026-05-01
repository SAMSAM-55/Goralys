<?php

namespace Goralys\App\Router\Data;

/**
 * DTO representing a named middleware binding with its optional parameters.
 */
final readonly class Middleware
{
    /** @param list<mixed> $params */
    public function __construct(
        public string $name,
        public array $params = [],
    ) {}
}
