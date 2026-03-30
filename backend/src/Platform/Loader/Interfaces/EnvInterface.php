<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\Loader\Interfaces;

interface EnvInterface
{
    public function load(string $path): bool;

    public function getByKey(string $key): mixed;
}
