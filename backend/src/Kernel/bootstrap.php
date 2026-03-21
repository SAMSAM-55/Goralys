<?php

use Goralys\Kernel\GoralysKernel;

// ----------- API bootstrap method ---------- //
function bootstrapAPI(GoralysKernel $kernel): void
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $forwardedOrigin = $_SERVER['HTTP_X_FORWARDED_ORIGIN'] ?? '';

    $effectiveOrigin = $origin !== '' ? $origin : $forwardedOrigin;
    $allowed = explode(",", $kernel->env->getByKey("ALLOWED_DOMAINS"));

    if (in_array($effectiveOrigin, $allowed)) {
        header("Access-Control-Allow-Origin: $effectiveOrigin");
        header('Access-Control-Allow-Credentials: true');
        header('Vary: Origin');
    }

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Max-Age: 86400'); // 1 day
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    // Preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204); // No content, but OK
        exit;
    }
}

// --------------- Kernel Init --------------- //
function bootKernel(bool $useFlash = false, bool $test = false, array $files = []): GoralysKernel
{
    $kernel = new GoralysKernel(__DIR__ . "/../../", $useFlash, $test, $files);
    $kernel->setHandlers();
    bootstrapAPI($kernel);
    return $kernel;
}
