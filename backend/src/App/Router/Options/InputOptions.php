<?php

namespace Goralys\App\Router\Options;

class InputOptions
{
    public const string FAIL_MESSAGE_KEY = 'on-fail-message';
    public const string FAIL_REDIRECT_KEY = 'on-fail-redirect';

    public static function require(string $input, string ...$_): array
    {
        $rules = [$input => ['required']];
        foreach ($_ as $v) {
            $rules[$v] = ['required'];
        }
        return [['input' => $rules]];
    }

    public static function min(string $input, int $min): array
    {
        $rules = [$input => ["min:$min"]];
        return [['input' => $rules]];
    }

    public static function onFailure(string $message, string $redirect = "/"): array
    {
        $rules = [self::FAIL_MESSAGE_KEY => $message, self::FAIL_REDIRECT_KEY => $redirect];
        return [['input' => $rules]];
    }
}
