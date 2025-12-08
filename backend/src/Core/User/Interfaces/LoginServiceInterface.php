<?php

namespace Goralys\Core\User\Interfaces;

use Goralys\Core\User\Data\UserLoginDTO;

interface LoginServiceInterface
{
    public function login(UserLoginDTO $userData): bool;
}
