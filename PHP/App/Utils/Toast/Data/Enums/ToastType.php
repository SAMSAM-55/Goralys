<?php

namespace Goralys\App\Utils\Toast\Data\Enums;

enum ToastType: string
{
    case SUCCESS = "success";
    case WARNING = "warning";
    case INFO = "info";
    case ERROR = "error";
    case UNKNOWN = "";
}
