<?php

namespace Goralys\Core\User\Interfaces;

use Goralys\Core\User\Data\UserRegisterDTO;

interface RegisterValidatorServiceInterface
{
    public function canRegister(UserRegisterDTO $data): bool;
}
