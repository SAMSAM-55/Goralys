<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Data;

use Goralys\Core\User\Data\Enums\UserRole;

/**
 * The DTO used to append a new user to the database
 */
readonly class UserCreateDTO
{
    public function __construct(
        public string $username,
        public string $fullName,
        public string $passwordHash,
        public UserRole $role
    ) {
    }
}
