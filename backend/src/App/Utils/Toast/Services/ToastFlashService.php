<?php

namespace Goralys\App\Utils\Toast\Services;

use Goralys\App\Utils\Toast\Data\FlashToastDTO;
use Goralys\App\Utils\Toast\Data\ToastDTO;
use Goralys\App\Utils\Toast\Interfaces\ToastFlashServiceInterface;
use Goralys\Shared\Exception\GoralysRuntimeException;

/**
 * This service is used to store and retrieve flash toasts.
 */
class ToastFlashService implements ToastFlashServiceInterface
{
    /**
     * Stores a new flash toast in the session.
     * As this is only supposed to be used in one request cycle, if a toast is already present, it will be overwritten.
     * @param ToastDTO $toastData The toast to store
     * @param ?string $action The action that should be performed when the toast is sent.
     * @return void
     */
    public function store(ToastDTO $toastData, ?string $action): void
    {
        $_SESSION["flash-toast"] = [
            "toastInfo" => $toastData->getToastInfo(),
            "redirect" => $toastData->getRedirect(),
            "flash" => true,
            "action" => $action
        ];
    }

    /**
     * Retrieves the flash toast inside the session.
     * Once the toast has been retrieved, it is removed from the session.
     * @return FlashToastDTO The flash toast in the session.
     * @throws GoralysRuntimeException If the flash toast was empty.
     */
    public function getToast(): FlashToastDTO
    {
        if (isset($_SESSION["flash-toast"])) {
            $toast = new FlashToastDTO(...$_SESSION["flash-toast"]);
            unset($_SESSION["flash-toast"]);
            return $toast;
        }

        throw new GoralysRuntimeException("No flash toast was found in the session");
    }
}
