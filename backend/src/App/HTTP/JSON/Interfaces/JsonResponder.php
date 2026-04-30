<?php

namespace Goralys\App\HTTP\JSON\Interfaces;

use JsonSerializable;

/**
 * Contract for services in charge of sending JSON data to the client.
 */
interface JsonResponder
{
    /**
     * Send the given data in the shape of a JSON response to the client.
     * @param array|JsonSerializable $data The data to send.
     * @param int $responseCode The HTTP code of the response to send.
     * @return void
     */
    public function send(array|JsonSerializable $data, int $responseCode = 200): void;
}
