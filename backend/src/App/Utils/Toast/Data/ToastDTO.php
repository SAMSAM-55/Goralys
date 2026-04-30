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
    public function __construct(
        readonly public array $toastInfo,
        public string $redirect,
        readonly public bool $flash = false
    ) {
    }
}
