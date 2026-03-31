<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Utils\Toast\Interfaces;

use Goralys\App\Utils\Toast\Data\ToastDTO;

interface ToastResponderInterface
{
    public function sendToast(ToastDTO $toastData): void;
}
