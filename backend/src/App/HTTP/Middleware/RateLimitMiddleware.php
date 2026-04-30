<?php

namespace Goralys\App\HTTP\Middleware;

use Goralys\App\HTTP\Middleware\Interface\MiddlewareInterface;
use Goralys\Kernel\GoralysKernel;

class RateLimitMiddleware implements MiddlewareInterface
{
    private string $endpoint;
    private ?string $redirect;
    private ?string $message;

    public function __construct(string $route, ...$params)
    {
        $this->endpoint = $params[0] ?? $route;
        $this->redirect = $params[1] ?? null;
        $this->message = $params[2] ?? null;
    }

    public function handle(GoralysKernel $kernel, callable $next): mixed
    {
        $kernel->requireRateLimit(
            $this->endpoint,
            $this->redirect ?? "/",
            $this->message ?? "Trop de requêtes. Veuillez réessayer plus tard."
        );

        return $next($kernel);
    }

    public static function for(string $endpoint, ?string $redirect = null, ?string $message = null): array
    {
        return ['rate-limit', [$endpoint, $redirect, $message]];
    }
}
