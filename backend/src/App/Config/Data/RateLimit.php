<?php

namespace Goralys\App\Config\Data;

readonly class RateLimit
{
    public function __construct(
        public int $maxRequests,
        public int $timeWindowSeconds,
        public RateLimitTimeMethod $timeMethod = RateLimitTimeMethod::CONSTANT,
        public int $maxLevels = 1
    ) {
    }

    public function getRate(): array
    {
        return [$this->maxRequests, $this->timeWindowSeconds];
    }
}
