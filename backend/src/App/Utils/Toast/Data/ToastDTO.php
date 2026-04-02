<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Utils\Toast\Data;

/**
 * The DTO used to transport the data of a toast
 */
readonly class ToastDTO
{
    public function __construct(
        public array $toastInfo,
        public string $redirect,
        public bool $flash = false
    ) {
    }
}
