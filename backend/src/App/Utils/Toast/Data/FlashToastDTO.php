<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Utils\Toast\Data;

class FlashToastDTO
{
    private array $toastInfo;
    private string $redirect;
    private bool $flash;
    private string $action;

    public function __construct(
        array $toastInfo,
        string $redirect,
        bool $flash = false,
        string $action = ""
    ) {
        $this->toastInfo = $toastInfo;
        $this->redirect = $redirect;
        $this->flash = $flash;
        $this->action = $action;
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
    final public function getAction(): string
    {
        return $this->action;
    }
}
