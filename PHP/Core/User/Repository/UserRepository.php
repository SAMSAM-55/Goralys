<?php

namespace Goralys\Core\User\Repository;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Data\UserFullDTO;
use Goralys\Core\User\Repository\Interfaces\UserRepositoryInterface;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use mysqli_result;

class UserRepository implements UserRepositoryInterface
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
     * @throws GoralysQueryException|GoralysPrepareException
     */
    public function getIdForUsername(string $username): int
    {
        $result = $this->db->fetch(
            "SELECT * FROM saje5795_goralys.users WHERE user_id = ?",
            "s",
            $username
        );

        if ($result->num_rows === 0) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "Could not find id for username : " . $username
            );
            return -1;
        }

        $this->logger->info(
            LoggerInitiator::CORE,
            "Successfully retrieved id for user : " . $username
        );
        return (int) $result->fetch_assoc()['id'];
    }

    /**
     * @param int $userId
     * @return UserFullDTO
     * @throws GoralysQueryException|GoralysPrepareException
     */
    public function getById(int $userId): UserFullDTO
    {
        $result = $this->db->fetch(
            "SELECT * FROM saje5795_goralys.users WHERE id = ?",
            "i",
            $userId
        );

        return $this->buildUserFromResult($result, "user id");
    }

    /**
     * @param string $username
     * @return UserFullDTO
     * @throws GoralysQueryException|GoralysPrepareException
     */
    public function getByUsername(string $username): UserFullDTO
    {
        $result = $this->db->fetch(
            "SELECT * FROM saje5795_goralys.users WHERE user_id = ?",
            "s",
            $username
        );

        return $this->buildUserFromResult($result, "username");
    }

    private function buildUserFromResult(mysqli_result $result, string $context): UserFullDTO
    {
        if ($result->num_rows === 0) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "Failed to fetch the user's data from the database, invalid " . $context
            );
            return new UserFullDTO(-1, "", UserRole::UNKNOWN, "");
        }

        $row = $result->fetch_assoc();

        $this->logger->info(
            LoggerInitiator::CORE,
            "User's data where successfully fetched"
        );
        return new UserFullDTO(
            (int) $row['id'],
            $row['user_id'],
            UserRole::fromString($row['role']),
            $row['full_name']
        );
    }
}
