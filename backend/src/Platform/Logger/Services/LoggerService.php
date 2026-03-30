<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

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
     * @param string $logDirectory The path to the "Logs" directory (you can name it as you want).
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
        if ($type === LoggerType::Debug && LoggerConfigLoader::getGoralysEnv() !== 'dev') {
            return;
        }

        $filename = LoggerConfigLoader::getInitiatorFile($initiator);
        $time = date("Y-m-d H:i:s");

        // Logs to the layer-specific log file
        if ($file = fopen(LoggerService::$logDirectory . $filename . ".log", "a")) {
            flock($file, LOCK_EX);

            fwrite(
                $file,
                "($initiator->value)[$type->name] at $time : $message" . PHP_EOL
            );

            fflush($file);
            flock($file, LOCK_UN);
            fclose($file);
        }

        // Logs to the global log file
        if ($file = fopen(LoggerService::$logDirectory . LoggerConfigLoader::getGlobalFile() . ".log", "a")) {
            flock($file, LOCK_EX);

            fwrite(
                $file,
                "($initiator->value)[$type->name] at $time : $message" . PHP_EOL
            );

            fflush($file);
            flock($file, LOCK_UN);
            fclose($file);
        }
    }
}
