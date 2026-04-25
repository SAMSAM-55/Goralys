<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

use Goralys\Kernel\GoralysKernel;

// ----------- API bootstrap method ---------- //
function bootstrapAPI(GoralysKernel $kernel): void
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $forwardedOrigin = $_SERVER['HTTP_X_FORWARDED_ORIGIN'] ?? '';

    $effectiveOrigin = $origin !== '' ? $origin : $forwardedOrigin;
    $allowed = array_map('trim', explode(",", $kernel->env->getByKey("ALLOWED_DOMAINS")));

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

    // Check if the user agent from the client is valid
    if (isset($_SESSION['current_id'])) {
        $ua = $_SESSION['ua'] ?? null;
        $uaHash = hash("sha256", $_SERVER['HTTP_USER_AGENT']);

        if (!$ua || $uaHash !== $ua) {
            session_unset();
            session_destroy();
            http_response_code(401); // Unauthorized
            exit;
        }

        if (!isset($_SESSION['regen_time']) || time() - $_SESSION['regen_time'] > 900) {
            session_regenerate_id(true);
            $_SESSION['regen_time'] = time();
        }
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
