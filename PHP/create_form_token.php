<?php

// session config
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => $_SERVER['HTTP_HOST'],
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if (isset($_SESSION['csrf-token'])) {
    exit();
}

$token = md5(uniqid(rand(), true));
$_SESSION['csrf-token'] = $token;

exit();
