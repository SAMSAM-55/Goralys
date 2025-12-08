<?php

namespace Goralys\Core\User\Interfaces;

use Goralys\Core\User\Data\Enums\UserRole;

interface GetUserRoleInterface
{
    public function getRoleByUsername(string $username): UserRole;
}
