<?php

namespace Goralys\App\RateLimiter;

use Goralys\App\Config\AppConfig;
use Goralys\App\Config\Data\RateLimitTimeMethod;
use Goralys\App\Config\RateLimiterConfig;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;

/**
 * File-based rate limiter that tracks request counts per IP address.
 * Supports constant, linear, and exponential back-off penalty windows.
 */
final class RateLimiter
{
    private const string ALGO = "sha256";
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger The injected logger.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Atomically writes `$data` to the file, truncating any previous content, then releases the lock.
     * @param resource $f The locked file handle.
     * @param string $data The serialized data to persist.
     * @return void
     */
    private function finalWrite($f, string $data): void
    {
        rewind($f);
        ftruncate($f, 0);
        fwrite($f, $data);
        flock($f, LOCK_UN);
        fclose($f);
    }

    /**
     * Checks whether the current request from the client's IP is within the rate limit for the given endpoint.
     * Increments the counter and updates the penalty window on each call.
     * Falls back to the general limit defined in {@see RateLimiterConfig::GENERAL} if no per-endpoint rule exists.
     * @param string $endpoint The name of the endpoint to check
     * (must match a key in {@see RateLimiterConfig::getRateLimits()}).
     * @return bool True if the request is allowed, false if the rate limit has been exceeded.
     */
    public function forwardRequest(string $endpoint): bool
    {
        $rate = RateLimiterConfig::getRateLimits()[$endpoint] ?? null;

        $filename = AppConfig::BASE_STORAGE_DIR
            . "RateLimiter/"
            . hash(self::ALGO, $endpoint)
            . ".txt";

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0o777, true);
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            $this->logger->warning(LoggerInitiator::APP, "Invalid IP address encountered: $ip");
            return false;
        }

        $fp = fopen($filename, 'c+');
        if (!$fp) {
            return true;
        }

        flock($fp, LOCK_EX);

        rewind($fp);
        $contents = stream_get_contents($fp);
        $data = json_decode($contents ?: '[]', true);

        if (!is_array($data)) {
            $data = [];
        }

        // init
        if (!isset($data[$ip])) {
            $data[$ip] = [
                'count' => 0,
                'reset_time' => 0,
                'failures' => 0,
            ];
        }

        $now = time();
        $limit = $rate?->maxRequests ?? RateLimiterConfig::GENERAL[0];
        $period = $rate?->timeWindowSeconds ?? RateLimiterConfig::GENERAL[1];

        $n = min(
            $rate?->maxLevels ?? 1,
            $data[$ip]['failures'] ?? 0,
        );

        $timeMethod = $rate?->timeMethod ?? RateLimitTimeMethod::CONSTANT;

        $penalty = match ($timeMethod) {
            RateLimitTimeMethod::CONSTANT => $period,
            RateLimitTimeMethod::LINEAR => $period * $n,
            RateLimitTimeMethod::EXPONENTIAL => min($period * (2 ** $n), 3600), // 1 hour max
        };

        $this->logger->debug(LoggerInitiator::APP, "Penalty for $endpoint(max: $rate?->maxLevels): $penalty");

        if ($data[$ip]['reset_time'] <= $now) {
            $data[$ip]['count'] = 0;
            $data[$ip]['reset_time'] = 0;
        }

        // limit check
        if ($data[$ip]['count'] >= $limit) {
            $data[$ip]['failures'] = min(
                $data[$ip]['failures'] + 1,
                $rate?->maxLevels ?? 1,
            );
            $this->finalWrite($fp, json_encode($data));
            return false;
        }

        $data[$ip]['count']++;
        $data[$ip]['reset_time'] = $now + $penalty;
        // clean up old entries
        foreach ($data as $ipKey => $entry) {
            if (($entry['reset_time'] ?? 0) < $now) {
                unset($data[$ipKey]);
            }
        }

        $this->finalWrite($fp, json_encode($data));
        return true;
    }
}
