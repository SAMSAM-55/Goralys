<?php

namespace Goralys\App\HTTP\Files\Interface;

use JetBrains\PhpStorm\NoReturn;

interface FileResponder
{
    public function send(string $path, string $name): void;
}
