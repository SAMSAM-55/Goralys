<?php

namespace Goralys\App\Utils\Toast\Data\Enums;

enum ToastType: string
{
    case SUCCESS = "success";
    case WARNING = "warning";
    case INFO = "info";
    case ERROR = "error";
    case UNKNOWN = "";

    public static function fromString(string $str): ToastType
    {
        $str = strtolower(trim($str));

        if ($str === "success") {
            return ToastType::SUCCESS;
        }
        if ($str === "info") {
            return ToastType::INFO;
        }
        if ($str === "warning") {
            return ToastType::WARNING;
        }
        if ($str === "error") {
            return ToastType::ERROR;
        }

        return ToastType::UNKNOWN;
    }
}
