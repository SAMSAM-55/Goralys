<?php

namespace Goralys\App\HTTP\Middleware;

use Goralys\App\Router\Data\Middleware;
use Goralys\Core\User\Data\Enums\UserRole;

class MiddlewareSets
{
    /**
     * @param string $action
     * @param UserRole $role
     * @param bool $strict
     * @param bool $transaction
     * @return list<Middleware>
     */
    public static function subjectsRoute(
        string $action,
        UserRole $role,
        bool $strict = true,
        bool $transaction = false
    ): array {
        return [
                new Middleware(...RateLimitMiddleware::for($action, '/subject')),
                new Middleware(...CSRFMiddleware::form($action, '/subject')),
                new Middleware(...AuthMiddleware::require()),
                new Middleware(...RoleMiddleware::require($role, $strict)),
                new Middleware(...($transaction ? DbMiddleware::transaction() : DbMiddleware::require())),
        ];
    }

    /**
     * @param string $action
     * @return list<Middleware>
     */
    public static function topicsRoute(string $action): array
    {
        return [
                new Middleware(...RateLimitMiddleware::for($action, '/subject')),
                new Middleware(...CSRFMiddleware::form($action, '/subject')),
                new Middleware(...AuthMiddleware::require()),
                new Middleware(...RoleMiddleware::require(UserRole::ADMIN, true)),
                new Middleware(...DbMiddleware::transaction()),
        ];
    }
}
