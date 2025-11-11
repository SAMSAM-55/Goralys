<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/utility.php';

use Goralys\Config\Config;
use Goralys\Utility\GoralysUtility;

Config::init();

session_start();
session_destroy();

echo "<script>
sessionStorage.clear()
sessionStorage.setItem('logged-in', 'false')
</script>";

GoralysUtility::showToast(
    'info',
    "Déconnexion",
    "Vous avez bien été déconnecté."
);
exit();
