<?php

namespace Goralys\Tests\Unit\Core;

use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Core\User\Services\LoginService;
use Goralys\Shared\Exception\User\UserNotFoundException;
use Goralys\Tests\Fakes\FakeGoralysLogger;
use Goralys\Tests\Fakes\FakeUserRepository;
use PHPUnit\Framework\TestCase;

class LoginServiceTest extends TestCase
{
    private FakeGoralysLogger $logger;
    private FakeUserRepository $repo;
    private LoginService $service;

    protected function setUp(): void
    {
        $this->logger = new FakeGoralysLogger();
        $this->repo = new FakeUserRepository();

        $this->service = new LoginService(
            $this->logger,
            $this->repo
        );
    }

    protected function tearDown(): void
    {
        unset($this->logger);
        unset($this->repo);
        unset($this->service);
    }

    public function testLoginNoUser()
    {
        $this->repo->setGetResult(null);

        try {
            $this->service->login(new UserLoginDTO("j.doe1", "foo"));
        } catch (UserNotFoundException $e) {
            self::assertEquals("No such user : j.doe1", $e->getMessage());
        }
    }

    /**
     * @throws UserNotFoundException
     */
    public function testLoginInvalidPassword()
    {
        $this->repo->setGetResult(new UserLoginDTO("j.doe1", "bar"));
        self::assertFalse($this->service->login(new UserLoginDTO("j.doe1", "foo")));
    }

    /**
     * @throws UserNotFoundException
     */
    public function testLoginWorks()
    {
        $this->repo->setGetResult(new UserLoginDTO("j.doe1", password_hash("foo", PASSWORD_DEFAULT)));
        self::assertTrue($this->service->login(new UserLoginDTO("j.doe1", "foo")));
    }
}
