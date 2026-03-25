<?php

namespace Goralys\Core\User\Repository\Interfaces;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Data\UserCreateDTO;
use Goralys\Core\User\Data\UserFullDTO;
use Goralys\Core\User\Data\UserLoginDTO;

interface UserRepositoryInterface
{
    public function getByUsername(string $username): UserFullDTO;
    public function exists(string $username): bool;
    public function isUsernameValid(string $username): bool;
    public function save(UserCreateDTO $userData): bool;
    public function getLoginDTO(string $username): ?UserLoginDTO;
    public function getRoleForUsername(string $username): ?UserRole;
    public function getFullNameForUsername(string $username): ?string;
    public function clearAll(): bool;
}
