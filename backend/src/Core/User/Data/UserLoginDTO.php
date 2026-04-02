<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Data;

/**
 * The DTO used to log in a user
 */
readonly class UserLoginDTO
{
    public function __construct(
        public string $username,
        public string $password
    ) {
    }
}
