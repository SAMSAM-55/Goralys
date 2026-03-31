<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Utils\Toast\Interfaces;

use Goralys\App\Utils\Toast\Data\Enums\ToastType;

interface ToastControllerInterface
{
    public function showToast(
        ToastType $toastType,
        string $toastTitle,
        string $toastMessage,
        string $redirect
    ): void;

    public function fatalError(
        int $responseCode,
        string $msg = "Une erreur interne est survenue.",
        string $redirect = "index.html",
        bool $flash = false
    ): void;
}
