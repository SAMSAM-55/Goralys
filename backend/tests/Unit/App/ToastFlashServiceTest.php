<?php

namespace Goralys\Tests\Unit\App;

use Goralys\App\Utils\Toast\Data\ToastDTO;
use Goralys\App\Utils\Toast\Services\ToastFlashService;
use Goralys\Shared\Exception\GoralysRuntimeException;
use PHPUnit\Framework\TestCase;

class ToastFlashServiceTest extends TestCase
{
    private ToastFlashService $service;
    protected function setUp(): void
    {
        $_SESSION = [];
        $this->service = new ToastFlashService();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        unset($this->service);
    }

    public function testStoreWritesToSession(): void
    {
        $toast = new ToastDTO(['toast' => true, 'toastType' => 'success'], "/home", true);
        $this->service->store($toast, "someAction");
        self::assertArrayHasKey("flash-toast", $_SESSION, "Expected flash-toast to be stored in session");
        self::assertSame("/home", $_SESSION["flash-toast"]["redirect"], "Expected redirect to be '/home'");
        self::assertSame("someAction", $_SESSION["flash-toast"]["action"], "Expected action to be 'someAction'");
        self::assertTrue($_SESSION["flash-toast"]["flash"], "Expected flash to be true");
    }

    public function testStoreOverwritesPreviousToast(): void
    {
        $toast1 = new ToastDTO(['toast' => true, 'toastType' => 'success'], "/first", true);
        $toast2 = new ToastDTO(['toast' => true, 'toastType' => 'error'], "/second", true);
        $this->service->store($toast1, "action1");
        $this->service->store($toast2, "action2");

        self::assertSame(
            "/second",
            $_SESSION["flash-toast"]["redirect"],
            "Expected second toast to overwrite the first",
        );
        self::assertSame(
            "action2",
            $_SESSION["flash-toast"]["action"],
            "Expected second action to overwrite the first",
        );
    }

    /**
     * @throws GoralysRuntimeException
     */
    public function testGetToastReturnsStoredToast(): void
    {
        $toast = new ToastDTO(['toast' => true, 'toastType' => 'info'], "/dashboard", true);
        $this->service->store($toast, "myAction");
        $flashToast = $this->service->getToast();
        self::assertSame("/dashboard", $flashToast->redirect, "Expected redirect to be '/dashboard'");
        self::assertSame("myAction", $flashToast->action, "Expected action to be 'myAction'");
    }

    /**
     * @throws GoralysRuntimeException
     */
    public function testGetToastRemovesFromSession(): void
    {
        $toast = new ToastDTO(['toast' => true, 'toastType' => 'warning'], "/page", true);
        $this->service->store($toast, "");
        $this->service->getToast();

        self::assertArrayNotHasKey(
            "flash-toast",
            $_SESSION,
            "Expected flash-toast to be removed from session after retrieval",
        );
    }

    public function testGetToastThrowsWhenNoToastInSession(): void
    {
        $this->expectException(GoralysRuntimeException::class);
        $this->service->getToast();
    }

    public function testStoreWithEmptyAction(): void
    {
        $toast = new ToastDTO(['toast' => true, 'toastType' => 'success'], "/home", true);
        $this->service->store($toast, "");

        self::assertSame(
            "",
            $_SESSION["flash-toast"]["action"],
            "Expected action to be empty string when not provided",
        );
    }
}
