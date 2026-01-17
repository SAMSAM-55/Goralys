<?php

use Goralys\Kernel\GoralysKernel;

// --------------- Kernel Init --------------- //
function bootKernel(bool $useFlash = false, bool $test = false, array $files = []): GoralysKernel
{
    $kernel = new GoralysKernel(__DIR__ . "/../../", $useFlash, $test, $files);
    $kernel->setHandlers();
    return $kernel;
}
