<?php

namespace Goralys\Platform\Logger\Services;

use Goralys\Platform\Logger\LoggerConfigLoader;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Data\Enums\LoggerType;

class LoggerService
{
    private static string $logDirectory;

    final public static function init(
        string $logDirectory
    ): void {
        LoggerService::$logDirectory = $logDirectory;
    }

    final public static function log(
        LoggerInitiator $initiator,
        LoggerType $type,
        string $message
    ): void {
        $filename = LoggerConfigLoader::getInitiatorFile($initiator);
        $time = date("Y-m-d H:i:s");

        // Log to the layer-specific log file
        if ($file = fopen(LoggerService::$logDirectory . $filename . ".log", "a")) {
            fwrite(
                $file,
                "($initiator->value)[$type->name] at $time : $message\n"
            );
        }

        // Log to the global log file
        if ($file = fopen(LoggerService::$logDirectory . LoggerConfigLoader::getGlobalFile() . ".log", "a")) {
            fwrite(
                $file,
                "($initiator->value)[$type->name] at $time : $message\n"
            );
        }
    }
}
