<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Subjects\Services;

use Goralys\Core\User\Repository\Interfaces\UserRepositoryInterface;
use RuntimeException;

class UsernameManager
{
    private UserRepositoryInterface $users;

    /**
     * Initializes the logger used by the service.
     * @param UserRepositoryInterface $users The injected user repository.
     */
    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    /**
     * @param string $username The username to append to the lookup table.
     * @return string The token linked to the username.
     */
    public function create(string $username): string
    {
        $publicId = $this->users->getPublicIdForUsername($username);

        if ($publicId === null) {
            throw new RuntimeException("Unknown username: $username");
        }

        return $publicId;
    }

    /**
     * Retrieves a username from its token inside the username lookup table.
     * @param string $id The token associated with the username.
     * @return string The username.
     */
    public function get(string $id): string
    {
        if (!$this->users->isPublicIdValid($id)) {
            throw new RuntimeException("Invalid user public id: $id");
        }

        return $this->users->getByPublicId($id)->username;
    }
}
