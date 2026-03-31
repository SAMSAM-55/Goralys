<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Data\UserCreateDTO;
use Goralys\Core\User\Interfaces\CreateUserInterface;
use Goralys\Core\User\Repository\Interfaces\UserRepositoryInterface;
use Goralys\Core\User\Repository\UserRepository;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

/**
 * The service used to create users.
 */
class CreateUserService implements CreateUserInterface
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
     * Creates a new user inside the database.
     * @param UserCreateDTO $userData The necessary data to create the user inside the database.
     * @return bool If the creation was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function createUser(UserCreateDTO $userData): bool
    {
        if ($userData->getRole() == UserRole::UNKNOWN) {
            return false;
        }
        return $this->repo->save($userData);
    }
}
