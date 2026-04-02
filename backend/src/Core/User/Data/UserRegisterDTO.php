<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Data;

/**
 * The DTO used to register a user
 */
readonly class UserRegisterDTO
{
    public function __construct(
        public string $username,
        public string $fullName,
        public string $password
    ) {
    }
}
