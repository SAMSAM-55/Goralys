<?php

namespace Goralys\Tests\Unit\Platform;

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Tests\Fakes\FakeGoralysLogger;
use PHPUnit\Framework\TestCase;

class GoralysLoggerTest extends TestCase
{
    private FakeGoralysLogger $logger;

    protected function setUp(): void
    {
        $this->logger = new FakeGoralysLogger();
    }

    public function testInfoLogsCorrectly(): void
    {
        $this->logger->info(LoggerInitiator::APP, "Info message");
        $logs = $this->logger->logs;
        self::assertCount(1, $logs);
        self::assertSame('INFO', $logs[0]['level']);
        self::assertSame(LoggerInitiator::APP, $logs[0]['initiator']);
        self::assertSame("Info message", $logs[0]['message']);
    }

    public function testDebugLogsCorrectly(): void
    {
        $this->logger->debug(LoggerInitiator::CORE, "Debug message");
        $logs = $this->logger->logs;
        self::assertCount(1, $logs);
        self::assertSame('DEBUG', $logs[0]['level']);
    }

    public function testWarningLogsCorrectly(): void
    {
        $this->logger->warning(LoggerInitiator::PLATFORM, "Warning message");
        $logs = $this->logger->logs;
        self::assertCount(1, $logs);
        self::assertSame('WARNING', $logs[0]['level']);
    }

    public function testErrorLogsCorrectly(): void
    {
        $this->logger->error(LoggerInitiator::KERNEL, "Error message");
        $logs = $this->logger->logs;
        self::assertCount(1, $logs);
        self::assertSame('ERROR', $logs[0]['level']);
    }

    public function testFatalLogsCorrectly(): void
    {
        $this->logger->fatal(LoggerInitiator::APP, "Fatal message");
        $logs = $this->logger->logs;
        self::assertCount(1, $logs);
        self::assertSame('FATAL', $logs[0]['level']);
    }

    public function testResetClearsLogs(): void
    {
        $this->logger->info(LoggerInitiator::APP, "Message");
        $this->logger->reset();
        self::assertEmpty($this->logger->logs);
    }

    public function testRotateDoesNothing(): void
    {
        // Should not throw or do anything
        $this->logger->rotate();
        self::assertEmpty($this->logger->logs);
    }
}
