<?php

namespace Goralys\App\Config\Data;

/**
 * DTO holding the configuration for a single rate-limit rule.
 */
final readonly class RateLimit
{
    public function __construct(
        public int $maxRequests,
        public int $timeWindowSeconds,
        public RateLimitTimeMethod $timeMethod = RateLimitTimeMethod::CONSTANT,
        public int $maxLevels = 1
    ) {
    }
}
