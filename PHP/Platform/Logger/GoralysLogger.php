<?php

namespace Goralys\Platform\Logger;

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Data\Enums\LoggerType;
use Goralys\Platform\Logger\Services\LoggerService;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;

class GoralysLogger implements LoggerInterface
{
    public function __construct()
    {
        LoggerConfigLoader::init();
        LoggerService::init(__DIR__ . "/../../Log/");
    }

    public function info(LoggerInitiator $initiator, string $message,): void
    {
        LoggerService::log(
            $initiator,
            LoggerType::Info,
            $message
        );
    }

    public function debug(LoggerInitiator $initiator, string $message,): void
    {
        LoggerService::log(
            $initiator,
            LoggerType::Debug,
            $message
        );
    }

    public function warning(LoggerInitiator $initiator, string $message,): void
    {
        LoggerService::log(
            $initiator,
            LoggerType::Warning,
            $message
        );
    }

    public function error(LoggerInitiator $initiator, string $message,): void
    {
        LoggerService::log(
            $initiator,
            LoggerType::Error,
            $message
        );
    }

    public function fatal(LoggerInitiator $initiator, string $message,): void
    {
        LoggerService::log(
            $initiator,
            LoggerType::Fatal,
            $message
        );
    }
}
