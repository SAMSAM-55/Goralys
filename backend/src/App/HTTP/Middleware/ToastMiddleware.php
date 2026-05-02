<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Middleware;

use Goralys\Kernel\GoralysKernel;

/**
 * Middleware that configures the toast delivery mode for a route.
 * When the flash option is active, toasts are stored in the session instead of being sent inline.
 */
final class ToastMiddleware implements Interface\MiddlewareInterface
{
    private const string FLASH = 'flash';
    private array $options;

    /**
     * @param string $route The matched route path.
     * @param mixed ...$params Accepts the 'flash' flag to enable flash-toast mode.
     */
    public function __construct(string $route, ...$params)
    {
        $this->options = $params;
    }

    /**
     * Activates flash-toast mode on the kernel when the 'flash' option is set.
     * @param GoralysKernel $kernel The application kernel.
     * @param callable $next The next handler in the pipeline.
     * @return mixed
     */
    public function handle(GoralysKernel $kernel, callable $next): mixed
    {
        if (in_array(self::FLASH, $this->options)) {
            $kernel->useFlash();
        }
        return $next($kernel);
    }

    /**
     * Returns the middleware binding that enables flash-toast mode.
     * @return array The middleware descriptor array.
     */
    public static function flash(): array
    {
        return ['toast', [self::FLASH]];
    }
}
