<?php

namespace Goralys\App\HTTP\Response\Interfaces;

use JetBrains\PhpStorm\NoReturn;
use JsonSerializable;

interface ResponseInterface
{
    #[NoReturn]
    public function http(): void;
    #[NoReturn]
    public function download(
        string $path,
        string $name,
        ?callable $after = null
    ): void;
    #[NoReturn]
    public function json(array|JsonSerializable $data, ?callable $after = null): void;
}
