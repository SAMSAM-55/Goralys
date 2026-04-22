<?php

namespace Goralys\App\HTTP\JSON\Interfaces;

use JsonSerializable;

interface JsonResponder
{
    public function send(array|JsonSerializable $data, int $responseCode = 500): void;
}
