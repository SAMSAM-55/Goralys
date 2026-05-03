<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Utils\Toast\Data\Enums;

/*
 * An enum representing a toast's type.
 */
enum ToastType: string
{
    case SUCCESS = "success";
    case WARNING = "warning";
    case INFO = "info";
    case ERROR = "error";
    case UNKNOWN = "";
}
