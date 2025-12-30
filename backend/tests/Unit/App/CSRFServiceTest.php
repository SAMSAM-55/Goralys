<?php

namespace Goralys\Tests\Unit\App;

use Goralys\App\Security\CSRF\Services\CSRFService;
use Goralys\Tests\Fakes\FakeGoralysLogger;
use Goralys\Tests\Fakes\FakeGoralysRequest;
use PHPUnit\Framework\TestCase;

class CSRFServiceTest extends TestCase
{
    private CSRFService $service;

    protected function setUp(): void
    {
        $_SESSION = [];
        $_SERVER = [];
        $this->service = new CSRFService(new FakeGoralysLogger());
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_SERVER = [];
        unset($this->service);
    }

    public function testCreateToken()
    {
        $this->service->create("foo");

        self::assertArrayHasKey(
            "foo",
            $_SESSION['csrf-tokens-table'],
            "Failed to create token for form 'foo'. Expected token to exist in session."
        );
    }

    public function testValidate()
    {
        $request = new FakeGoralysRequest();
        $request->setInput(['csrf-token' => "foo-xy"]);
        $_SESSION['csrf-tokens-table']['bar'] = "foo-xy";
        $_SESSION['csrf-tokens-table']['bar1'] = "foo-xyz";

        self::assertTrue(
            $this->service->validate("bar", $request),
            "Validation failed for form 'bar' with matching token 'foo-xy'"
        );

        self::assertFalse(
            $this->service->validate("bar1", $request),
            "Validation passed for form 'bar1' when it shouldn't have with token 'foo-xyz'"
        );

        $request = new FakeGoralysRequest();
        $request->setInput([]);
        unset($_SESSION['csrf-tokens-table']);

        self::assertFalse(
            $this->service->validate("foo2", $request),
            "Validation passed for form 'foo2' when no token was provided"
        );

        $request = new FakeGoralysRequest();
        $request->setInput([]);
        $_SESSION['csrf-tokens-table']['bar3'] = "foo-xyab";

        self::assertFalse(
            $this->service->validate("bar3", $request),
            "Validation passed for form 'bar3' with no token provided in request"
        );
    }

    public function testGetForForm()
    {
        $_SESSION['csrf-tokens-table']['foo'] = "foo-xy";
        self::assertSame(
            "foo-xy",
            $this->service->getForForm("foo"),
            "Failed to retrieve token for form 'foo' which should be 'foo-xy'"
        );

        self::assertSame(
            "",
            $this->service->getForForm("bar"),
            "Retrieved token for non-existing form 'bar' which shouldn't have a token"
        );

        unset($_SESSION['csrf-tokens-table']);
        self::assertSame(
            "",
            $this->service->getForForm("foo"),
            "Retrieved token for form 'foo' after session tokens were cleared"
        );
    }
}
