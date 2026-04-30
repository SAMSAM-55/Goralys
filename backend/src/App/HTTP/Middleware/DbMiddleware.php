<?php

namespace Goralys\App\HTTP\Middleware;

use Goralys\Kernel\GoralysKernel;
use Throwable;

class DbMiddleware implements Interface\MiddlewareInterface
{
    private const string TRANSACTION = 'transaction';
    private array $options;

    public function __construct(string $route, ...$params)
    {
        $this->options = $params;
    }

    /**
     * @throws Throwable
     */
    public function handle(GoralysKernel $kernel, callable $next): mixed
    {
        $kernel->requireDb();
        $transaction = in_array(self::TRANSACTION, $this->options, true);

        if (!$transaction) {
            return $next($kernel);
        }

        try {
            $kernel->db->beginTransaction();

            $result = $next($kernel);

            $kernel->db->commit();

            return $result;
        } catch (Throwable $e) {
            $kernel->db->rollback();
            throw $e;
        }
    }

    public static function require(): array
    {
        return ['db'];
    }

    public static function transaction(): array
    {
        return ['db', [self::TRANSACTION]];
    }
}
