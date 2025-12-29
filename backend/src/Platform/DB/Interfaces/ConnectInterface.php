<?php

namespace Goralys\Platform\DB\Interfaces;

use Goralys\Platform\DB\Data\DbDto;
use mysqli;

interface ConnectInterface
{
    public function connectToDatabase(DbDto $credentials): mysqli|null;
}
