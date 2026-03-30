<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\DB\Interfaces;

use Goralys\Platform\DB\Data\DbDto;
use mysqli;

interface ConnectInterface
{
    public function connectToDatabase(DbDto $credentials): mysqli|null;
}
