<?php

namespace Goralys\App\RateLimiter;

use Goralys\App\Config\AppConfig;
use Goralys\App\Config\Data\RateLimitTimeMethod;
use Goralys\App\Config\RateLimiterConfig;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;

class RateLimiter
{
    private const string ALGO = "sha256";
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function finalWrite($f, string $data): void
    {
        rewind($f);
        ftruncate($f, 0);
        fwrite($f, $data);
        flock($f, LOCK_UN);
        fclose($f);
    }

    public function forwardRequest(string $endpoint): bool
    {
        $rate = RateLimiterConfig::getRateLimits()[$endpoint] ?? null;

        $filename = AppConfig::BASE_STORAGE_DIR
            . "RateLimiter/"
            . hash(self::ALGO, $endpoint)
            . ".txt";

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
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
                'failures' => 0
            ];
        }

        $now = time();
        $limit = $rate?->maxRequests ?? RateLimiterConfig::GENERAL[0];
        $period = $rate?->timeWindowSeconds ?? RateLimiterConfig::GENERAL[1];

        $n = min(
            $rate?->maxLevels ?? 1,
            $data[$ip]['failures'] ?? 0
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
                $rate?->maxLevels ?? 1
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
