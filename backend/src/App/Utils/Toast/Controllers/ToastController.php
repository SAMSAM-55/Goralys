<?php

namespace Goralys\App\Utils\Toast\Controllers;

use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\App\Utils\Toast\Interfaces\ToastControllerInterface;
use Goralys\App\Utils\Toast\Services\ToastBuilderService;
use Goralys\App\Utils\Toast\Services\ToastResponderService;
use JetBrains\PhpStorm\NoReturn;

/**
 * The controller that manages the toasts interactions with the frontend..
 */
class ToastController implements ToastControllerInterface
{
    private ToastBuilderService $builder;
    private ToastResponderService $responder;

    /**
     * Initializes the toast responder and builder for the controller.
     */
    public function __construct()
    {
        $this->builder = new ToastBuilderService();
        $this->responder = new ToastResponderService();
    }

    /**
     * Sends a toast to the frontend.
     * The toast can be sent as  a script that redirects the user
     * or as a JSON object that will be parsed by the frontend.
     * @param ToastType $toastType The type of the toast, there are four types:
     * - success
     * - info
     * - warning
     * - error
     * @param string $toastTitle The title of the toast.
     * @param string $toastMessage The message of the toast.
     * @param string $redirect The page to redirect the user to.
     * @param bool $isJS If the toast should be sent as a JSON or not (default =  false).
     * @return void
     */
    public function showToast(
        ToastType $toastType,
        string $toastTitle,
        string $toastMessage,
        string $redirect,
        bool $isJS = false
    ): void {
        $toastData = $this->builder->buildToast($toastType, $toastTitle, $toastMessage, $redirect, $isJS);
        $this->responder->sendToast($toastData);
    }

    /**
     * A simple macro to send error toasts.
     * @param int $responseCode The HTTP response code to send.
     * @param string $msg The message of the toast (default = "Une erreur interne est survenue.").
     * @param string $redirect The page to redirect the user to (default = "index.html").
     * @return void
     */
    #[NoReturn]
    public function fatalError(
        int $responseCode,
        string $msg = "Une erreur interne est survenue.",
        string $redirect = "index.html"
    ): void {
        http_response_code($responseCode);
        $this->showToast(
            ToastType::ERROR,
            "Erreur",
            $msg,
            $redirect
        );
        exit;
    }
}
