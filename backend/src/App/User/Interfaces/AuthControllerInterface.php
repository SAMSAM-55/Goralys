<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\User\Interfaces;

use Goralys\App\User\Data\Enums\UserAuthStatus;
use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Core\User\Data\UserRegisterDTO;

interface AuthControllerInterface
{
    public function register(UserRegisterDTO $userData): bool;
    public function login(UserLoginDTO $userData): bool;
    public function logout(): bool;

    public function getAuthStatus(int $sinceLastConnection): UserAuthStatus;
}
