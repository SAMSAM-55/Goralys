<?php

namespace Goralys\Core\User\Interfaces;

use Goralys\Core\User\Data\UserCreateDTO;
use Goralys\Platform\DB\Facade\DbContainer;

interface CreateUserInterface
{
    public function createUser(UserCreateDTO $userData): bool;
}
