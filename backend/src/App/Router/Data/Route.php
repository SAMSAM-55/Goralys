<?php

namespace Goralys\App\Router\Data;

use Closure;

class Route
{
    /** @param list<Middleware> $middlewares */
    public function __construct(
        readonly public string $route,
        readonly public string $method,
        readonly public Closure $handler,
        readonly public array $options,
        public array $middlewares = []
    ) {
    }

    public function middleware(string $name, mixed ...$params): self
    {
        $this->middlewares[] = new Middleware($name, ...$params);
        return $this;
    }

    public function middlewares(Middleware ...$middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->middlewares[] = $middleware;
        }
        return $this;
    }
}
