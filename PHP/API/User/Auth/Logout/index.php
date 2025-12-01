<?php

use Goralys\App\Utils\Toast\Controllers\ToastController;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;

session_start();

session_unset();
session_destroy();

http_response_code(200); // OK

$toast = new ToastController();
$toast->showToast(
    ToastType::SUCCESS,
    "Déconnexion",
    "Vous avez bien été déconnecté.",
    "index.html"
);
exit;
