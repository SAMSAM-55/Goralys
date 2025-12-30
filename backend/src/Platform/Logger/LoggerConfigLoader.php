<?php

namespace Goralys\Platform\Logger;

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;

/**
 * A small loader to load the necessary configuration for the logger.
 */
class LoggerConfigLoader
{
    private static array $loggerFiles;
    private static string $goralysEnv;
    private static bool $isInitialized = false;

    /**
     * Initializes the respective log files for the different layers and for all instances of the loader (static array).
     * @return void
     */
    final public static function init(): void
    {
        if (!self::$isInitialized) {
            self::$loggerFiles = [
                    "APP" => $_ENV["LOGGER_APP_FILENAME"] ?? "Goralys_App",
                    "CORE" => $_ENV["LOGGER_CORE_FILENAME"] ?? "Goralys_Core",
                    "PLATFORM" => $_ENV["LOGGER_PLATFORM_FILENAME"] ?? "Goralys_Platform",
                    "GLOBAL" => $_ENV["LOGGER_GLOBAL_FILENAME"] ?? "Goralys_Global"
            ];
            self::$goralysEnv = $_ENV['GORALYS_ENVIRONMENT'];
            self::$isInitialized = true;
        }
    }

    /**
     * Returns the name of the log file for the given layer.
     * Note that the full path is constructed inside the `LoggerService`.
     * @param LoggerInitiator $type The layer to get the log file for.
     * @return string The name of the file.
     */
    final public static function getInitiatorFile(LoggerInitiator $type): string
    {
        return self::$loggerFiles[$type->name];
    }

    /**
     * Get the global log file
     * Note that the full path is constructed inside the `LoggerService`.
     * @return string The name of the file
     */
    final public static function getGlobalFile(): string
    {
        return self::$loggerFiles["GLOBAL"];
    }

    /**
     * Get the current environment.
     * The two possible values are 'dev' and 'prod'.
     * @return string The current environment.
     */
    public static function getGoralysEnv(): string
    {
        return self::$goralysEnv;
    }
}
