<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION = [];
$_POST = [];
$_GET = [];

error_reporting(E_ALL);
ini_set('display_errors', '1');
