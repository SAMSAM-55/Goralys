<?php

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Data\UserCreateDTO;
use Goralys\Core\User\Interfaces\CreateUserInterface;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

class CreateUserService implements CreateUserInterface
{
    private DbContainer $db;

    public function __construct(
        DbContainer $db
    ) {
        $this->db = $db;
    }

    /**
     * @param UserCreateDTO $userData
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function createUser(UserCreateDTO $userData): bool
    {
        if ($userData->getRole() == UserRole::UNKNOWN) {
            return false;
        }

        return $this->db->run(
            "INSERT INTO saje5795_goralys.users (user_id, full_name, password_hash, role) VALUES (?, ?, ?, ?)",
            "ssss",
            $userData->getUsername(),
            $userData->getFullName(),
            $userData->getPasswordHash(),
            $userData->getRole()->toString()
        );
    }
}
