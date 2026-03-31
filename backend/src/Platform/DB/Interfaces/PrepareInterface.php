<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\DB\Interfaces;

use Goralys\Platform\DB\Data\StmtDto;
use mysqli_stmt;

interface PrepareInterface
{
    public function prepare(
        string $query
    ): mysqli_stmt;
    public function prepareAndBind(
        StmtDto $stmtData
    ): mysqli_stmt;
}
