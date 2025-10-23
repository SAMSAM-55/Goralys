<?php
require_once __DIR__ . '/config.php';

session_start();
session_destroy();

echo "<script>
sessionStorage.clear()
sessionStorage.setItem('logged-in', 'false')
</script>";

show_toast('info',
    "Déconnexion",
    "Vous avez bien été déconnecté.");
exit();