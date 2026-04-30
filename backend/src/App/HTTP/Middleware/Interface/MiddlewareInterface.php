<?php

namespace Goralys\App\HTTP\Middleware\Interface;

use Goralys\Kernel\GoralysKernel;

interface MiddlewareInterface
{
    public function __construct(string $route, mixed ...$params);

    public function handle(GoralysKernel $kernel, callable $next): mixed;
}
