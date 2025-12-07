<?php

namespace Goralys\Core\User\Data;

use Goralys\Core\User\Data\Enums\UserRole;

/**
 * The DTO used to append a new user to the database
 */
class UserCreateDTO
{
    private string $username;
    private string $fullName;
    private string $passwordHash;
    private UserRole $role;

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
