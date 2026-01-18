<?php

use Goralys\Kernel\GoralysKernel;

// --------------- Kernel Init --------------- //
function bootKernel(bool $useFlash = false, bool $test = false, array $files = []): GoralysKernel
{
    header("Access-Control-Allow-Origin: https://goralys.fr");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header('Access-Control-Allow-Credentials: true');
    header("Access-Control-Max-Age: 84600"); // cache for a day

    $kernel = new GoralysKernel(__DIR__ . "/../../", $useFlash, $test, $files);
    $kernel->setHandlers();
    return $kernel;
}
