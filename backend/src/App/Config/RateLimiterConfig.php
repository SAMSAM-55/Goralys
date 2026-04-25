<?php

namespace Goralys\App\Config;

use Goralys\App\Config\Data\RateLimit;
use Goralys\App\Config\Data\RateLimitTimeMethod;

class RateLimiterConfig
{
    public const array GENERAL = [100, 60];

    /**
     * @return array<string, RateLimit>
     */
    public static function getRateLimits(): array
    {
        return [
            'login' => new RateLimit(3, 60, RateLimitTimeMethod::EXPONENTIAL, 5),
            'register' => new RateLimit(5, 180, RateLimitTimeMethod::LINEAR, 3)
        ];
    }
}
