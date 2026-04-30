<?php

namespace Goralys\App\HTTP\Response\Interfaces;

use JetBrains\PhpStorm\NoReturn;
use JsonSerializable;

interface ImmediateResponseInterface
{
    #[NoReturn]
    public function http(): never;
    #[NoReturn]
    public function download(
        string $path,
        string $name,
        ?callable $after = null
    ): never;
    #[NoReturn]
    public function json(array|JsonSerializable $data, ?callable $after = null): never;
}
