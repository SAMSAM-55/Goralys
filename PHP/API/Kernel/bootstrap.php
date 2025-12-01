<?php

use Goralys\API\Kernel\GoralysKernel;

// --------------- Kernel Init --------------- //
function bootKernel(): GoralysKernel
{
    $kernel = new GoralysKernel(__DIR__ . "/../../../");
    $kernel->setHandlers();
    return $kernel;
}
