<?php

namespace Goralys\Tests\Unit\App;

use Goralys\App\Utils\Toast\Data\ToastDTO;
use Goralys\App\Utils\Toast\Services\ToastFlashService;
use Goralys\App\Utils\Toast\Services\ToastResponderService;
use PHPUnit\Framework\TestCase;

class ToastResponderServiceTest extends TestCase
{
    private ToastFlashService $flashService;
    private ToastResponderService $service;

    protected function setUp(): void
    {
        $_SESSION = [];
        $this->flashService = new ToastFlashService();
        $this->service = new ToastResponderService($this->flashService);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        unset($this->flashService);
        unset($this->service);
    }

    public function testSendFlashToastStoresInSession(): void
    {
        $toast = new ToastDTO(['toast' => true, 'toastType' => 'success'], "/home", true);
        $this->service->sendToast($toast, "myAction");

        self::assertArrayHasKey("flash-toast", $_SESSION, "Expected flash toast to be stored in session");
        self::assertSame("/home", $_SESSION["flash-toast"]["redirect"], "Expected redirect to be '/home'");
        self::assertSame("myAction", $_SESSION["flash-toast"]["action"], "Expected action to be 'myAction'");
    }

    public function testSendFlashToastDoesNotOutput(): void
    {
        $toast = new ToastDTO(['toast' => true, 'toastType' => 'success'], "/home", true);

        ob_start();
        $this->service->sendToast($toast, "myAction");
        $output = ob_get_clean();

        self::assertSame("", $output, "Expected no output when sending a flash toast");
    }

    public function testSendNonFlashToastOutputsJson(): void
    {
        $toastInfo = [
            'toast' => true,
            'toastType' => 'error',
            'toastTitle' => 'Err',
            'toastMessage' => 'Oops',
            'redirect' => '/err',
        ];
        $toast = new ToastDTO($toastInfo, "/err", false);

        ob_start();
        $this->service->sendToast($toast);
        $output = ob_get_clean();

        $decoded = json_decode($output, true);
        self::assertIsArray($decoded, "Expected JSON output for non-flash toast");
        self::assertTrue($decoded['toast'], "Expected 'toast' key to be true in JSON output");
        self::assertSame("error", $decoded['toastType'], "Expected toastType to be 'error' in JSON output");
    }

    public function testSendNonFlashToastDoesNotStoreInSession(): void
    {
        $toast = new ToastDTO(['toast' => true, 'toastType' => 'info'], "/info", false);

        ob_start();
        $this->service->sendToast($toast);
        ob_get_clean();

        self::assertArrayNotHasKey("flash-toast", $_SESSION, "Expected non-flash toast not to be stored in session");
    }
}
