<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Utils\Toast\Controllers;

use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\App\Utils\Toast\Interfaces\ToastControllerInterface;
use Goralys\App\Utils\Toast\Services\ToastBuilderService;
use Goralys\App\Utils\Toast\Services\ToastFlashService;
use Goralys\App\Utils\Toast\Services\ToastResponderService;
use JetBrains\PhpStorm\NoReturn;

/**
 * The controller that manages the toasts interactions with the frontend.
 */
class ToastController implements ToastControllerInterface
{
    private ToastBuilderService $builder;
    public readonly ToastFlashService $flashService;
    private ToastResponderService $responder;

    /**
     * Initializes the toast responder and builder for the controller.
     */
    public function __construct()
    {
        $this->builder = new ToastBuilderService();
        $this->flashService = new ToastFlashService();
        $this->responder = new ToastResponderService($this->flashService);
    }

    /**
     * Sends a toast to the frontend.
     * The toast can be sent as a JSON object that will be parsed by the frontend
     * or stored as a flash toast.
     * @param ToastType $toastType The type of the toast, there are four types:
     * - success
     * - info
     * - warning
     * - error
     * @param string $toastTitle The title of the toast.
     * @param string $toastMessage The message of the toast.
     * @param string $redirect The page to redirect the user to.
     * @param bool $flash If the toast message is flash or not.
     * @param string $action The action to perform when the toast is sent to the frontend.
     * @return void
     */
    public function showToast(
        ToastType $toastType,
        string $toastTitle,
        string $toastMessage,
        string $redirect,
        bool $flash = false,
        string $action = ""
    ): void {
        $toastData = $this->builder->buildToast($toastType, $toastTitle, $toastMessage, $redirect, $flash);
        $this->responder->sendToast($toastData, $action);
    }

    /**
     * A simple macro to send error toasts.
     * @param int $responseCode The HTTP response code to send.
     * @param string $msg The message of the toast (default = "Une erreur interne est survenue.").
     * @param string $redirect The page to redirect the user to (default = "index.html").
     * @param bool $flash If the toast is flash or not.
     * @return never
     */
    #[NoReturn]
    public function fatalError(
        int $responseCode,
        string $msg = "Une erreur interne est survenue.",
        string $redirect = "index.html",
        bool $flash = false
    ): never {
        http_response_code($responseCode);
        $this->showToast(
            ToastType::ERROR,
            "Erreur",
            $msg,
            $redirect,
            $flash
        );
        exit;
    }
}
