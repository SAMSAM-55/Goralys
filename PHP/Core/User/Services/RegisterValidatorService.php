<?php

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\Core\User\Interfaces\RegisterValidatorServiceInterface;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

class RegisterValidatorService implements RegisterValidatorServiceInterface
{
    private DbContainer $db;

    public function __construct(
        DbContainer $db
    ) {
        $this->db = $db;
    }


    /**
     * @param UserRegisterDTO $data
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function canRegister(UserRegisterDTO $data): bool
    {

        $exits = $this->db->fetch(
            "SELECT * FROM saje5795_goralys.users WHERE user_id = ? LIMIT 1",
            "s",
            $data->getUsername()
        )->num_rows != 0;

        $valid = $this->db->fetch(
            "SELECT user_id FROM
            (SELECT student_id AS user_id FROM saje5795_goralys.student_topics
            UNION ALL
            SELECT teacher_id AS user_id FROM saje5795_goralys.topics
            ) AS all_ids
            WHERE user_id = ?
            LIMIT 1",
            "s",
            $data->getUsername()
        )->num_rows != 0;

        return $valid && !$exits;
    }
}
