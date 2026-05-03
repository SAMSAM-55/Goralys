<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Interfaces;

use Goralys\Core\User\Data\Enums\UserRole;

/**
 * Contract for a service capable of resolving a user's role from their username.
 */
interface GetUserRoleInterface
{
    /**
     * @param string $username The user's username.
     * @return UserRole The role assigned to the user.
     */
    public function getRoleByUsername(string $username): UserRole;
}
