<?php

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Interfaces\GetUserRoleInterface;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

class GetUserRoleService implements GetUserRoleInterface
{
    private DbContainer $db;

    public function __construct(
        DbContainer $db
    ) {
        $this->db = $db;
    }

    /**
     * Returns a user's role based on his username
     * @param string $username The user's name
     * @return UserRole The user's role
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function getRoleByUsername(string $username): UserRole
    {
        $result = $this->db->fetch(
            "SELECT user_id, role FROM (
            SELECT student_id AS user_id, 'student' AS role
            FROM saje5795_goralys.student_topics
            UNION ALL
            SELECT teacher_id AS user_id, 'teacher' AS role
            FROM saje5795_goralys.topics
            UNION ALL
            SELECT user_id AS user_id, 'admin' AS role
            FROM saje5795_goralys.admins_list
            ) AS all_ids
            WHERE user_id = ?
            LIMIT 1",
            "s",
            $username
        );

        if ($result->num_rows == 0) {
            return UserRole::UNKNOWN;
        }

        $row = $result->fetch_assoc();
        return UserRole::fromString($row['role']);
    }
}
