<?php

namespace Goralys\Core\User\Data;

use Goralys\Core\User\Data\Enums\UserRole;

class UserBaseDTO
{
    private readonly string $username;
    private readonly UserRole $role;

    public function __construct(
        string $username,
        UserRole $role
    ) {
        $this->username = $username;
        $this->role = $role;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }
}
