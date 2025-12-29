<?php

namespace Goralys\App\Utils\Toast\Services;

// Loader
use Goralys\App\Utils\AppConfig;
// Toast specific classes
use Goralys\App\Utils\Toast\Data\ToastDTO;
use Goralys\App\Utils\Toast\Interfaces\ToastResponderInterface;

/**
 * The service used to send a toast to the frontend
 */
class ToastResponderService implements ToastResponderInterface
{
    private ToastFlashService $flashService;

    public function __construct(ToastFlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    /**
     * Sends a toast to the frontend.
     * If the `isJS` property is set to `true`, the toast will be sent as a JSON object and then parsed by the frontend.
     * Else, the toast will be sent via url params that will then be read by the frontend to display the toast.
     * @param ToastDTO $toastData The data of the toast.
     * @param string $action The action to perform when the toast is sent to the frontend.
     * @return void
     */
    public function sendToast(ToastDTO $toastData, string $action = ""): void
    {
        if ($toastData->isFlash()) {
            $this->flashService->store($toastData, $action);
            return;
        }

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($toastData->getToastInfo(), JSON_UNESCAPED_UNICODE);
    }
}
