<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Interfaces\GetUserRoleInterface;
use Goralys\Core\User\Repository\Interfaces\UserRepositoryInterface;
use Goralys\Shared\Exception\User\UserNotFoundException;

/**
 * Service used to retrieve and automatically assign a user's role.
 */
class GetUserRoleService implements GetUserRoleInterface
{
    private UserRepositoryInterface $repo;

    /**
     * Initializes the user repository used by the service.
     * @param UserRepositoryInterface $repo The injected user repository.
     */
    public function __construct(
        UserRepositoryInterface $repo
    ) {
            $this->repo = $repo;
    }

    /**
     * Returns a user's role based on his username.
     * @param string $username The user's name.
     * @return UserRole The user's role.
     * @throws UserNotFoundException If the user could not be found.
     */
    public function getRoleByUsername(string $username): UserRole
    {
        $role = $this->repo->getRoleForUsername($username);

        if ($role === null) {
            throw new UserNotFoundException("No such user : " . $username);
        }

        return $role;
    }
}
