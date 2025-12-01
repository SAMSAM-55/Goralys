<?php

namespace Goralys\App\Utils\Toast\Services;

use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\App\Utils\Toast\Interfaces\ToastBuilderInterface;
use Goralys\App\Utils\Toast\Data\ToastDTO;

class ToastBuilderService implements ToastBuilderInterface
{
    public function buildToast(
        ToastType $toastType,
        string $toastTitle,
        string $toastMessage,
        string $redirect,
        bool $isJS = false
    ): ToastDTO {
        if ($isJS) {
            $toastInfo = [
                "toast" => true,
                "toastType" => $toastType->value,
                "toastTitle" => $toastTitle,
                "toastMessage" => $toastMessage,
                "redirect" =>  $redirect
            ];
        } else {
            $toastInfo = [
                "toast" => "true",
                "toastType" => $toastType->value,
                "toastTitle" => $toastTitle,
                "toastMessage" => $toastMessage,
            ];
        }
        return new ToastDTO(
            $toastInfo,
            $redirect,
            $isJS
        );
    }
}
