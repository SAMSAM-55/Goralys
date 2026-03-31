<?php

namespace Goralys\Tests\Unit\App;

use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\App\Utils\Toast\Services\ToastBuilderService;
use PHPUnit\Framework\TestCase;

class ToastBuilderServiceTest extends TestCase
{
    private ToastBuilderService $service;
    protected function setUp(): void
    {
        $this->service = new ToastBuilderService();
    }

    protected function tearDown(): void
    {
        unset($this->service);
    }

    public function testBuildToastReturnsCorrectToastInfo(): void
    {
        $toast = $this->service->buildToast(ToastType::SUCCESS, "Success", "Operation completed", "/home");
        $info = $toast->getToastInfo();
        self::assertTrue($info['toast'], "Expected 'toast' to be true");
        self::assertSame(ToastType::SUCCESS->value, $info['toastType'], "Expected toastType to be 'success'");
        self::assertSame("Success", $info['toastTitle'], "Expected toastTitle to be 'Success'");
        self::assertSame(
            "Operation completed",
            $info['toastMessage'],
            "Expected toastMessage to be 'Operation completed'"
        );
        self::assertSame("/home", $info['redirect'], "Expected redirect to be '/home'");
    }

    public function testBuildToastReturnsCorrectRedirect(): void
    {
        $toast = $this->service->buildToast(ToastType::ERROR, "Error", "Something went wrong", "/error");
        self::assertSame("/error", $toast->getRedirect(), "Expected redirect to be '/error'");
    }

    public function testBuildToastDefaultFlashIsFalse(): void
    {
        $toast = $this->service->buildToast(ToastType::INFO, "Info", "Just info", "/info");
        self::assertFalse($toast->isFlash(), "Expected flash to be false by default");
    }

    public function testBuildToastWithFlashTrue(): void
    {
        $toast = $this->service->buildToast(ToastType::WARNING, "Warning", "Be careful", "/warn", true);
        self::assertTrue($toast->isFlash(), "Expected flash to be true when explicitly set");
    }

    public function testBuildToastAllToastTypes(): void
    {
        foreach (ToastType::cases() as $type) {
            $toast = $this->service->buildToast($type, "Title", "Message", "/");
            self::assertSame(
                $type->value,
                $toast->getToastInfo()['toastType'],
                "Expected toastType to match for type $type->name"
            );
        }
    }
}
