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
readonly class UserFullDTO
{
    public function __construct(
        public int $id,
        public string $username,
        public UserRole $role,
        public string $fullName
    ) {
    }
}
