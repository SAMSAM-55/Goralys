<?php

namespace Goralys\Core\User\Interfaces;

use Goralys\Core\User\Data\UserRegisterDTO;

interface RegisterServiceInterface
{
    public function register(UserRegisterDTO $data): bool;
}
