<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Interfaces;

use Goralys\Core\User\Data\UserCreateDTO;

interface CreateUserInterface
{
    public function createUser(UserCreateDTO $userData): bool;
}
