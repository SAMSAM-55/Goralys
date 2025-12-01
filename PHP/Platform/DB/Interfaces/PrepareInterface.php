<?php

namespace Goralys\Platform\DB\Interfaces;

use Goralys\Platform\DB\Data\StmtDto;
use mysqli_stmt;

interface PrepareInterface
{
    public function prepareAndBind(
        StmtDto $stmtData
    ): mysqli_stmt;
}
