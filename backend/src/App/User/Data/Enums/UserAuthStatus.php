<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\User\Data\Enums;

/**
 * This enum is used to represent the status of the user's authentification.
 * There are free status possible:
 * - AUTHENTICATED When the user is already authenticated.
 * - SESSION_EXPIRED When the user's session has expired (1 hour by default).
 * - NOT_AUTHENTICATED When the user is not authenticated or has not been active since 2 hours (2x session lifetime).
 */
enum UserAuthStatus: int
{
    case AUTHENTICATED = 1;
    case SESSION_EXPIRED = 0;
    case NOT_AUTHENTICATED = -1;
}
