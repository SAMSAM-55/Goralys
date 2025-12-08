<?php

namespace Goralys\App\User\Interfaces;

use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Core\User\Data\UserRegisterDTO;

interface AuthControllerInterface
{
    public function register(UserRegisterDTO $userData): bool;
    public function login(UserLoginDTO $userData): bool;
    public function logout(): bool;
}
