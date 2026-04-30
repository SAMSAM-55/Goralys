<?php

namespace Goralys\App\HTTP\Middleware;

use Goralys\Kernel\GoralysKernel;

class ToastMiddleware implements Interface\MiddlewareInterface
{
    private const string FLASH = 'flash';
    private array $options;

    public function __construct(string $route, ...$params)
    {
        $this->options = $params;
    }

    public function handle(GoralysKernel $kernel, callable $next): mixed
    {
        if (in_array(self::FLASH, $this->options)) {
            $kernel->useFlash();
        }
        return $next($kernel);
    }

    public static function flash(): array
    {
        return ['toast', [self::FLASH]];
    }
}
