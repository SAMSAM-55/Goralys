<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Shared\Utils;

use Goralys\Shared\Utils\String\StringUtils;

class UtilitiesManager
{
    public StringUtils $string;

    public function __construct()
    {
        $this->string = new StringUtils();
    }
}
