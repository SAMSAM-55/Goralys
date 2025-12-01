<?php

namespace Goralys\App\Utils\Toast\Controllers;

use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\App\Utils\Toast\Interfaces\ToastControllerInterface;
use Goralys\App\Utils\Toast\Services\ToastBuilderService;
use Goralys\App\Utils\Toast\Services\ToastResponderService;
use JetBrains\PhpStorm\NoReturn;

class ToastController implements ToastControllerInterface
{
    private ToastBuilderService $builder;
    private ToastResponderService $responder;

    public function __construct()
    {
        $this->builder = new ToastBuilderService();
        $this->responder = new ToastResponderService();
    }

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
     * @param int $responseCode
     * @param string $msg
     * @param string $redirect
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
