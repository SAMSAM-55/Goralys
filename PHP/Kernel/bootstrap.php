<?php

use Goralys\Kernel\GoralysKernel;

// --------------- Kernel Init --------------- //
function bootKernel(): GoralysKernel
{
    $kernel = new GoralysKernel(__DIR__ . "/../../Goralys/");
    $kernel->setHandlers();
    return $kernel;
}
