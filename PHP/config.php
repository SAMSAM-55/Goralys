<?php

namespace Goralys\Config;

require "vendor/autoload.php";

use Dotenv\Dotenv;
use mysqli;

final class Config
{
    //-------------- Database --------------//
    public static string $DATABASE_HOST;
    public static string $DATABASE_ID;
    public static string $DATABASE_PASSWORD;
    public static string $DATABASE_NAME;
    //---------------- Mail ----------------//
    public static string $MAIL_DOMAIN;
    public static string $MAIL_USER;
    public static string $MAIL_PASSWORD;
    //---------------- Misc ----------------//
    private static  bool $INITIALIZED = false;
    public const string FOLDER = "/goralys/"; // Just use for development, should be "" for production

    final public static function init(): void
    {
        if (self::$INITIALIZED) {
            return;
        }

        $dotenv = Dotenv::createImmutable(__DIR__ . "/..");

        if ($dotenv->load()) {
            self::$DATABASE_HOST = $_ENV['DATABASE_HOST'];
            self::$DATABASE_ID = $_ENV['DATABASE_ID'];
            self::$DATABASE_NAME = $_ENV['DATABASE_NAME'];
            self::$DATABASE_PASSWORD = $_ENV['DATABASE_PASSWORD'];

            self::$MAIL_DOMAIN = $_ENV['MAIL_DOMAIN'];
            self::$MAIL_USER = $_ENV['MAIL_USER'];
            self::$MAIL_PASSWORD = $_ENV['MAIL_PASSWORD'];

            self::$INITIALIZED = true;
        }
    }

    final public static function connectToDatabase(): mysqli|null
    {
        $conn = new mysqli(Config::$DATABASE_HOST, Config::$DATABASE_ID, Config::$DATABASE_PASSWORD, Config::$DATABASE_NAME);
        $conn->set_charset('utf8mb4');

        if ($conn->connect_error) {
            http_response_code(500); // Internal Server Error
            error_log("Connection failed: " . $conn->connect_error);
            return null;
        }

        return $conn;
    }
}
