<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Request\Interfaces;

interface RequestInterface
{
    public function get(string $key): mixed;
    public function validate(string $key1, string ...$_): bool;
}
