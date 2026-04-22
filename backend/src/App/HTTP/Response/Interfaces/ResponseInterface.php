<?php

namespace Goralys\App\HTTP\Response\Interfaces;

use JetBrains\PhpStorm\NoReturn;

interface ResponseInterface
{
    #[NoReturn]
    public function download(string $path, string $name): void;
}
