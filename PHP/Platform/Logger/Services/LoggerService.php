<?php

namespace Goralys\Platform\Logger\Services;

use Goralys\Platform\Logger\LoggerConfigLoader;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Data\Enums\LoggerType;

/**
 * Main logging service.
 * It provides a global `log` method to log to the correct file.
 */
class LoggerService
{
    private static string $logDirectory;

    /**
     * Initializes the log path for all instances of the service.
     * @param string $logDirectory The path to the "Log" directory (you can name it as you want).
     * @return void
     */
    final public static function init(
        string $logDirectory
    ): void {
        LoggerService::$logDirectory = $logDirectory;
    }

    /**
     * Functions used to log a message
     * @param LoggerInitiator $initiator The initiator (layer) of the log.
     * @param LoggerType $type The type of the log, there are currently five types:
     * - debug
     * - info
     * - warning
     * - error
     * - fatal
     * @param string $message The message to log.
     * @return void
     */
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
