<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Utils\Toast\Data;

/**
 * The DTO used to transport the data of a toast
 */
class ToastDTO
{
    private array $toastInfo;
    private string $redirect;
    private bool $flash;

    public function __construct(
        array $toastInfo,
        string $redirect,
        bool $flash = false
    ) {
        $this->toastInfo = $toastInfo;
        $this->redirect = $redirect;
        $this->flash = $flash;
    }

    // Getters
    final public function getToastInfo(): array
    {
        return $this->toastInfo;
    }
    final public function getRedirect(): string
    {
        return $this->redirect;
    }
    final public function isFlash(): bool
    {
        return $this->flash;
    }
}
