<?php

use Goralys\Kernel\GoralysKernel;

// --------------- Kernel Init --------------- //
function bootKernel(bool $useFlash = false): GoralysKernel
{
    $kernel = new GoralysKernel(__DIR__ . "/../../", $useFlash);
    $kernel->setHandlers();
    return $kernel;
}
