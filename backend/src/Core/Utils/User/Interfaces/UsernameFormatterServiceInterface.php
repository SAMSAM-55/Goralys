<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Utils\User\Interfaces;

interface UsernameFormatterServiceInterface
{
    public function formatUsername(string $username): string;
}
