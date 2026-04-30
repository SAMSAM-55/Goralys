<?php

namespace Goralys\App\HTTP\Middleware;

use Goralys\App\HTTP\Middleware\Interface\MiddlewareInterface;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Kernel\GoralysKernel;

class RoleMiddleware implements MiddlewareInterface
{
    private UserRole $role;
    private bool $strict;

    public function __construct(string $route, ...$params)
    {
        $this->role = $params[0];
        $this->strict = $params[1] ?? false;
    }

    public function handle(GoralysKernel $kernel, callable $next): mixed
    {
        $kernel->requireRole($this->role, $this->strict);
        return $next($kernel);
    }

    public static function require(UserRole $role, bool $strict = false): array
    {
        return ['role', [$role, $strict]];
    }
}
