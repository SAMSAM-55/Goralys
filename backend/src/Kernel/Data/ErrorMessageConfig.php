<?php

namespace Goralys\Kernel\Data;

readonly class ErrorMessageConfig
{
    public function __construct(
        public string $message,
        public string $redirect = "/",
        public int $code = 500
    ) {
    }
}
