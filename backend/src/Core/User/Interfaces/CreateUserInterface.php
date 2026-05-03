<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Interfaces;

use Goralys\Core\User\Data\UserCreateDTO;

/**
 * Contract for a service capable of persisting a new user to the database.
 */
interface CreateUserInterface
{
    /**
     * @param UserCreateDTO $userData The data required to create the new user.
     * @return bool If the creation was successful or not.
     */
    public function createUser(UserCreateDTO $userData): bool;
}
