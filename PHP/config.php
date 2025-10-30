<?php

namespace Goralys\Config;

use mysqli;

final class Config
{
    //-------------- Database --------------//
    public const string SERVERNAME = "localhost";
    public const string DATABASEID = "your db user";
    public const string DATABASEPASSWORD = "your db password";
    public const string DATABASENAME = "your db name";
    //---------------- Mail ----------------//
    public const string MAILDOMAIN = "your mail domain";
    public const string MAILUSER = "your mail user (adress)";
    public const string MAILPASSWORD = "your mail password";
    //---------------- Misc ----------------//
    public const string FOLDER = "/goralys/"; // Just use for development, should be "" for production

    final public static function connectToDatabase(): mysqli|null
    {
        $conn = new mysqli(Config::SERVERNAME, Config::DATABASEID, Config::DATABASEPASSWORD, Config::DATABASENAME);
        $conn->set_charset('utf8mb4');

        if ($conn->connect_error) {
            http_response_code(500); // Internal Server Error
            error_log("Connection failed: " . $conn->connect_error);
            return null;
        }

        return $conn;
    }
}
