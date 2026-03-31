<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Data;

use Goralys\Core\User\Data\Enums\UserRole;

/**
 * The DTO containing all the information of a user
 */
class UserFullDTO
{
    private int $id;
    private string $username;
    private UserRole $role;

    private string $fullName;

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
