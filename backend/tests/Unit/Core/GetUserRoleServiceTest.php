<?php

namespace Goralys\Tests\Unit\Core;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Services\GetUserRoleService;
use Goralys\Core\Utils\User\Services\UsernameFormatterService;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use Goralys\Shared\Exception\User\UserNotFoundException;
use Goralys\Tests\Fakes\FakeUserRepository;
use PHPUnit\Framework\TestCase;

class GetUserRoleServiceTest extends TestCase
{
    private FakeUserRepository $repo;
    private GetUserRoleService $service;

    protected function setUp(): void
    {
        $this->repo = new FakeUserRepository();
        $this->formatter = new UsernameFormatterService();

        $this->service = new GetUserRoleService(
            $this->repo
        );
    }

    protected function tearDown(): void
    {
        unset($this->repo);
        unset($this->service);
    }

    public function testGetRoleByUsernameNoUser()
    {
        $this->repo->setGetResult(null);

        try {
            $this->service->getRoleByUsername("j.doe1");
        } catch (GoralysPrepareException | GoralysQueryException | UserNotFoundException $e) {
            self::assertEquals("No such user : j.doe1", $e->getMessage());
        }
    }

    public function testGetRoleByUsernameRoleUnknown()
    {
        $this->repo->setGetResult(UserRole::UNKNOWN);
        self::assertEquals(UserRole::UNKNOWN, $this->service->getRoleByUsername("j.doe1"));
    }

    public function testGetRoleByUsernameWorks()
    {
        $this->repo->setGetResult(UserRole::STUDENT);
        self::assertEquals(UserRole::STUDENT, $this->service->getRoleByUsername("j.doe1"));
    }
}
