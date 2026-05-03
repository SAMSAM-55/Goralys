<?php

use Goralys\App\HTTP\Middleware\RateLimitMiddleware;
use Goralys\App\Router\GoralysRouter;
use Goralys\Kernel\GoralysKernel;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Shared\Exception\GoralysRuntimeException;

function createToastRoutes(GoralysRouter $router): void
{
    // ================================================
    // [SECTION] Flash toast
    // ================================================
    $router->get('toast/flash', function (GoralysKernel $kernel) {
        try {
            $kernel->logger->debug(
                LoggerInitiator::APP,
                "Attempting to retrieve the flash toast, current session: " . print_r($_SESSION, true),
            );
            $toast = $kernel->toast->flashService->getToast();
            $kernel->logger->debug(
                LoggerInitiator::APP,
                "Successfully retrieved the flash toast.",
            );
            $kernel->response()->json([
                "success" => true,
                "toast" => $toast->toastInfo,
                "action" => $toast->action,
            ]);
        } catch (GoralysRuntimeException) {
            $kernel->logger->debug(
                LoggerInitiator::APP,
                "Failed to retrieved the flash toast.",
            );
            $kernel->response()->json(['success' => false]);
        }
    })
    ->middleware(...RateLimitMiddleware::for("flash-toast"));
}
