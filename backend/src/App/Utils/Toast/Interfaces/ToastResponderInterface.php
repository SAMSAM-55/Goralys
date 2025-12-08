<?php

namespace Goralys\App\Utils\Toast\Interfaces;

use Goralys\App\Utils\Toast\Data\ToastDTO;

interface ToastResponderInterface
{
    public function sendToast(ToastDTO $toastData): void;
}
