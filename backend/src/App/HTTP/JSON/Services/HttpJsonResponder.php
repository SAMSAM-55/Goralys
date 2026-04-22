<?php

namespace Goralys\App\HTTP\JSON\Services;

use Goralys\App\HTTP\JSON\Interfaces\JsonResponder;
use Goralys\Shared\Exception\GoralysRuntimeException;
use JsonSerializable;

class HttpJsonResponder implements JsonResponder
{
    /**
     * @throws GoralysRuntimeException
     */
    public function send(array|JsonSerializable $data, int $responseCode = 200): void
    {
        if (headers_sent($file, $line)) {
            throw new GoralysRuntimeException("Headers were already sent at $file:$line");
        }

        http_response_code($responseCode);
        header("Content-Type: application/JSON; charset: utf-8");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
