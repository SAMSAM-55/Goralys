<?php

namespace Goralys\Tests\Unit\App;

use Goralys\App\Subjects\Services\SubjectsUsernameManager;
use Goralys\Tests\Fakes\FakeGoralysLogger;
use PHPUnit\Framework\TestCase;

class SubjectsUsernameManagerTest extends TestCase
{
    private FakeGoralysLogger $logger;
    private SubjectsUsernameManager $service;
    protected function setUp(): void
    {
        $_SESSION = [];
        $this->logger = new FakeGoralysLogger();
        $this->service = new SubjectsUsernameManager($this->logger);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        unset($this->logger);
        unset($this->service);
    }

    public function testStoreReturnsNonEmptyToken(): void
    {
        $token = $this->service->store("j.doe");
        self::assertNotEmpty($token, "Expected store() to return a non-empty token");
    }

    public function testStoreWritesUsernameToSession(): void
    {
        $token = $this->service->store("j.doe");
        self::assertArrayHasKey(
            $token,
            $_SESSION["username-table"],
            "Expected token to exist in session username-table"
        );
        self::assertSame(
            "j.doe",
            $_SESSION["username-table"][$token],
            "Expected username to be stored under the token"
        );
    }

    public function testStoreDifferentUsernamesGetDifferentTokens(): void
    {
        $token1 = $this->service->store("j.doe");
        $token2 = $this->service->store("a.smith");
        self::assertNotSame($token1, $token2, "Expected different tokens for different usernames");
    }

    public function testGetReturnsStoredUsername(): void
    {
        $token = $this->service->store("e.martin");
        $result = $this->service->get($token);
        self::assertSame("e.martin", $result, "Expected get() to return the username stored under the token");
    }

    public function testStoreMultipleUsernamesAllRetrievable(): void
    {
        $usernames = ["j.doe", "a.smith", "e.martin"];
        $tokens = [];
        foreach ($usernames as $username) {
            $tokens[$username] = $this->service->store($username);
        }
        foreach ($usernames as $username) {
            self::assertSame(
                $username,
                $this->service->get($tokens[$username]),
                "Expected get() to return '$username' for its token"
            );
        }
    }
}
