<?php

namespace Goralys\App\HTTP\Files\Interface;

/**
 * An interface for file-responding services.
 */
interface FileResponder
{
    /**
     * Sends a file to the frontend.
     * @param string $path The path of the file to send.
     * @param string $name The name of the file when downloaded by the client.
     * @return void
     */
    public function send(string $path, string $name): void;
}
