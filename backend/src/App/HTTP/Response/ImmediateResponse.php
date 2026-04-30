<?php

namespace Goralys\App\HTTP\Response;

use Goralys\App\HTTP\Files\Interface\FileResponder;
use Goralys\App\HTTP\JSON\Interfaces\JsonResponder;
use Goralys\App\HTTP\Response\Interfaces\ImmediateResponseInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use JetBrains\PhpStorm\NoReturn;
use JsonSerializable;
use Throwable;

class ImmediateResponse implements ImmediateResponseInterface
{
    private int $code;
    private LoggerInterface $logger;
    private FileResponder $files;
    private JsonResponder $json;

    public function __construct(int $code, LoggerInterface $logger, FileResponder $files, JsonResponder $json)
    {
        $this->code = $code;
        $this->logger = $logger;
        $this->files = $files;
        $this->json = $json;
    }

    /**
     * @param string $path
     * @param string $name
     * @param callable|null $after
     * @throws Throwable
     */
    #[NoReturn]
    public function download(string $path, string $name, ?callable $after = null): never
    {
        try {
            $this->files->send($path, $name);
            $this->logger->info(LoggerInitiator::APP, "File $path was successfully downloaded");
            try {
                ($after ?? fn() => null)();
            } catch (Throwable $e) {
                $this->logger->warning(
                    LoggerInitiator::APP,
                    "Post response callback failed. \n Error: " . $e->getMessage()
                );
            }
            http_response_code($this->code);
            exit;
        } catch (Throwable $e) {
            $this->logger->error(LoggerInitiator::APP, "Failed to download file: $path\n Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param array|JsonSerializable $data
     * @param callable|null $after
     * @throws Throwable
     */
    #[NoReturn]
    public function json(array|JsonSerializable $data, ?callable $after = null): never
    {
        try {
            $json = json_encode($data);
            $size = $json !== false ? strlen($json) : 0;

            $this->json->send($data);
            $this->logger->info(LoggerInitiator::APP, "Successfully sent JSON (size: $size)");
            if ($size < 250) {
                $this->logger->debug(LoggerInitiator::APP, print_r($json, true));
            }
            try {
                ($after ?? fn() => null)();
            } catch (Throwable $e) {
                $this->logger->warning(
                    LoggerInitiator::APP,
                    "Post response callback failed. \n Error: " . $e->getMessage()
                );
            }
            http_response_code($this->code);
            exit;
        } catch (Throwable $e) {
            $this->logger->error(
                LoggerInitiator::APP,
                "Failed to send JSON (size: $size)\n Error: " . $e->getMessage()
            );
            throw $e;
        }
    }

    /**
     * @return never
     */
    #[NoReturn]
    public function http(): never
    {
        http_response_code($this->code);
        exit;
    }
}
