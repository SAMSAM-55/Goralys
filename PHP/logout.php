<?php

use Goralys\Utility\GoralysUtility;

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
