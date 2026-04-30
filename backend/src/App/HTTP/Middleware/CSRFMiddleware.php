<?php

namespace Goralys\App\HTTP\Middleware;

use Goralys\App\HTTP\Middleware\Interface\MiddlewareInterface;
use Goralys\Kernel\GoralysKernel;

class CSRFMiddleware implements MiddlewareInterface
{
    private string $redirect;
    private string $formId;

    public function __construct(string $route, ...$params)
    {
        $this->formId = $params[0] ?? '';
        $this->redirect = $params[1] ?? "/";
    }

    public function handle(GoralysKernel $kernel, callable $next): mixed
    {
        $kernel->requireCSRF($this->formId, $this->redirect);
        return $next($kernel);
    }

    public static function form(string $formId, ?string $redirect = null): array
    {
        return ['csrf', [$formId, $redirect]];
    }
}
