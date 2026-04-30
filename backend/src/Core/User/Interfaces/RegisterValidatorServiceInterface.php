<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Interfaces;

use Goralys\Core\User\Data\UserRegisterDTO;

/**
 * Contract for a service that validates whether a user is eligible to register.
 */
interface RegisterValidatorServiceInterface
{
    /**
     * @param UserRegisterDTO $data The registration data to validate.
     * @return bool If the user is allowed to register or not.
     */
    public function canRegister(UserRegisterDTO $data): bool;
}
