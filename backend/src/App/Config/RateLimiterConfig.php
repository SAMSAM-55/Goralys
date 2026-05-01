<?php

namespace Goralys\App\Config;

use Goralys\App\Config\Data\RateLimit;
use Goralys\App\Config\Data\RateLimitTimeMethod;

/**
 * Configuration for the rate limiter.
 * Defines the general fallback limits as well as per-endpoint rules.
 */
final class RateLimiterConfig
{
    public const array GENERAL = [100, 60];

    /**
     * @return array<string, RateLimit>
     */
    public static function getRateLimits(): array
    {
        return [
            'login' => new RateLimit(3, 60, RateLimitTimeMethod::EXPONENTIAL, 5),
            'register' => new RateLimit(5, 180, RateLimitTimeMethod::LINEAR, 3),
            'flash-toast' => new RateLimit(100, 60, RateLimitTimeMethod::CONSTANT),
            'csrf-create' => new RateLimit(30, 60, RateLimitTimeMethod::CONSTANT),
        ];
    }
}
