<?php

namespace Goralys\Platform\Logger;

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;

class LoggerConfigLoader
{
    private static array $loggerFiles;
    private static bool $isInitialized = false;

    final public static function init(): void
    {
        if (!self::$isInitialized) {
            self::$loggerFiles = [
                    "APP" => $_ENV["LOGGER_APP_FILENAME"] ?? "Goralys_App",
                    "CORE" => $_ENV["LOGGER_CORE_FILENAME"] ?? "Goralys_Core",
                    "PLATFORM" => $_ENV["LOGGER_PLATFORM_FILENAME"] ?? "Goralys_Platform",
                    "GLOBAL" => $_ENV["LOGGER_GLOBAL_FILENAME"] ?? "Goralys_Global"
            ];
            self::$isInitialized = true;
        }
    }

    final public static function getInitiatorFile(LoggerInitiator $type): string
    {
        return self::$loggerFiles[$type->name];
    }

    final public static function getGlobalFile(): string
    {
        return self::$loggerFiles["GLOBAL"];
    }
}
