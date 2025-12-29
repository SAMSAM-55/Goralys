<?php

namespace Goralys\App\Utils\Toast\Interfaces;

use Goralys\App\Utils\Toast\Data\Enums\ToastType;

interface ToastBuilderInterface
{
    public function buildToast(
        ToastType $toastType,
        string $toastTitle,
        string $toastMessage,
        string $redirect,
        bool $flash = false
    );
}
