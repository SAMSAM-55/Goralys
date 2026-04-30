<?php

namespace Goralys\App\HTTP\Middleware;

use Goralys\App\HTTP\Middleware\Interface\MiddlewareInterface;
use Goralys\Kernel\GoralysKernel;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;

class AuthMiddleware implements MiddlewareInterface
{
    private string $endpoint;
    private array $options;

    public function __construct(string $endpoint, mixed ...$params)
    {
        $this->endpoint = $endpoint;
        $this->options = $params;
    }

    /**
     * @param GoralysKernel $kernel
     * @param callable $next
     * @return mixed
     */
    public function handle(GoralysKernel $kernel, callable $next): mixed
    {
        if (in_array('weak', $this->options)) {
            $kernel->logger->debug(LoggerInitiator::APP, "Running weak auth middleware\n" . print_r($_SESSION, true));
            if (!$kernel->checkAuth()) {
                unset($_SESSION["current_username"]);
                unset($_SESSION["current_role"]);
                unset($_SESSION["current_id"]);
                unset($_SESSION["current_full_name"]);
                $kernel->response(401)->http(); // Unauthorized
            }

            return $next($kernel);
        }
        $kernel->logger->debug(LoggerInitiator::APP, "Running string auth middleware\n" . print_r($_SESSION, true));
        $kernel->requireAuth($this->endpoint);
        return $next($kernel);
    }

    public static function require(): array
    {
        return ['auth'];
    }

    public static function weak(): array
    {
        return ['auth', ['weak']];
    }
}
