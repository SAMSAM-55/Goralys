<?php

namespace Goralys\Tests\Fakes;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Interfaces\GetUserRoleInterface;

class FakeGetUserRole implements GetUserRoleInterface
{
    public UserRole $role = UserRole::STUDENT {
        set {
            $this->role = $value;
        }
    }

    public function getRoleByUsername(string $username): UserRole
    {
        return $this->role;
    }

}
