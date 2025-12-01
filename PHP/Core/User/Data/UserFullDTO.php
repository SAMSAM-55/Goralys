<?php

namespace Goralys\Core\User\Data;

use Goralys\Core\User\Data\Enums\UserRole;

class UserFullDTO
{
    private readonly int $id;
    private readonly string $username;
    private readonly UserRole $role;

    private readonly string $fullName;

    public function __construct(
        int $id,
        string $username,
        UserRole $role,
        string $fullName
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->role = $role;
        $this->fullName = $fullName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }
}
