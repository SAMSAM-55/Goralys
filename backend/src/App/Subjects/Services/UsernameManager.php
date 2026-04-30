<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Subjects\Services;

use Goralys\Core\User\Repository\Interfaces\UserRepositoryInterface;
use Goralys\Core\User\Repository\UserRepository;
use RuntimeException;

/**
 * Manages the mapping between real usernames and their opaque public tokens,
 * delegating lookups to the user repository.
 */
final class UsernameManager
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
     * 'Creates' a token by retrieving the public id of the user.
     * @param string $username The username to get the public id of.
     * @return string The public id linked to the username.
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
     * Retrieves a username from its public id inside the DB using {@see UserRepositoryI::getPublicIdForUsername()}.
     * @param string $id The public id associated with the username.
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
