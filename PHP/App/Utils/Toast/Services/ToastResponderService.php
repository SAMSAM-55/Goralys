<?php

namespace Goralys\App\Utils\Toast\Services;

// Loader
use Goralys\App\Utils\AppConfig;
// Toast specific classes
use Goralys\App\Utils\Toast\Data\ToastDTO;
use Goralys\App\Utils\Toast\Interfaces\ToastResponderInterface;

class ToastResponderService implements ToastResponderInterface
{
    public function sendToast(ToastDTO $toastData): void
    {
        if ($toastData->isJs()) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            echo json_encode($toastData->getToastInfo(), JSON_UNESCAPED_UNICODE);
        } else {
            $app = new AppConfig();
            $query = http_build_query($toastData->getToastInfo());
            $base = $app->getFolder() . $toastData->getRedirect();
            echo "<script type='text/javascript'>
            window.location.href = window.location.origin + '$base' + '?$query';
            </script>";
        }
    }
}
