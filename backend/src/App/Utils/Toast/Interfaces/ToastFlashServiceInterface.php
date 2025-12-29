<?php

namespace Goralys\App\Utils\Toast\Interfaces;

use Goralys\App\Utils\Toast\Data\FlashToastDTO;
use Goralys\App\Utils\Toast\Data\ToastDTO;

interface ToastFlashServiceInterface
{
    public function store(ToastDTO $toastData, ?string $action): void;
    public function getToast(): FlashToastDTO;
}
