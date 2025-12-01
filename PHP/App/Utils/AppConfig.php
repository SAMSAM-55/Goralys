<?php

namespace Goralys\App\Utils;

class AppConfig
{
    private static string $folder;

    public function __construct()
    {
        self::$folder = $_ENV["FOLDER"];
    }

    final public function getFolder(): string
    {
        return self::$folder;
    }
}
