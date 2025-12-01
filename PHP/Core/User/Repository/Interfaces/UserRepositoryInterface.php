<?php

namespace Goralys\Core\User\Repository\Interfaces;

use Goralys\Core\User\Data\UserFullDTO;

interface UserRepositoryInterface
{
    public function getIdForUsername(string $username): int;
    public function getById(int $userId): UserFullDTO;
    public function getByUsername(string $username): UserFullDTO;
}
