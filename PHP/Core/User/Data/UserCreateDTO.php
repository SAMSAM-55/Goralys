<?php

namespace Goralys\Core\User\Data;

use Goralys\Core\User\Data\Enums\UserRole;

class UserCreateDTO
{
    private readonly string $username;
    private readonly string $fullName;
    private readonly string $passwordHash;
    private readonly UserRole $role;

    public function __construct(
        string $username,
        string $fullName,
        string $passwordHash,
        UserRole $role
    ) {
        $this->username = $username;
        $this->fullName = $fullName;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }
}
