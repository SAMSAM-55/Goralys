<?php

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Core\User\Interfaces\LoginServiceInterface;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

class LoginService implements LoginServiceInterface
{
    private GoralysLogger $logger;
    private DbContainer $db;

    public function __construct(
        GoralysLogger $logger,
        DbContainer $db
    ) {
        $this->logger = $logger;
        $this->db = $db;
    }

    /**
     * @param UserLoginDTO $userData
     * @return bool
     * @throws GoralysQueryException|GoralysPrepareException
     */
    public function login(UserLoginDTO $userData): bool
    {
        $result = $this->db->fetch(
            "SELECT * FROM saje5795_goralys.users WHERE user_id = ?",
            "s",
            $userData->getUsername()
        );

        if ($result->num_rows === 0) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "Failed to connect user, invalid username : " . $userData->getUsername()
            );
            return false;
        }

        $passwordHash = $result->fetch_assoc()['password_hash'];

        if (!password_verify($userData->getPassword(), $passwordHash)) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "Failed to connect user, invalid password " . " for user : " . $userData->getUsername()
            );
            return false;
        }

        $this->logger->info(
            LoggerInitiator::CORE,
            "New user logged in : " . $userData->getUsername()
        );
        return true;
    }
}
