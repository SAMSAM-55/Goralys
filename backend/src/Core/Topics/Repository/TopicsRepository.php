<?php

namespace Goralys\Core\Topics\Repository;

use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

class TopicsRepository implements Interfaces\TopicsRepositoryInterface
{
    private DbContainer $db;

    public function __construct(
        DbContainer $db
    ) {
        $this->db = $db;
    }

    /**
     * @param int $topicId
     * @param string $topicCode
     * @param string $topicName
     * @return void
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function insertTopic(int $topicId, string $topicCode, string $topicName): void
    {
        $this->db->run(
            "INSERT INTO topics (id, topic_code, name) VALUES (?, ?, ?)",
            "iss",
            $topicId,
            $topicCode,
            $topicName
        );
    }

    /**
     * @param int $topicId
     * @param string $teacherUsername
     * @return void
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function insertTeacher(int $topicId, string $teacherUsername): void
    {
        $this->db->run(
            "INSERT INTO topic_teachers (topic_id, teacher_id) VALUES (?, ?)",
            "is",
            $topicId,
            $teacherUsername
        );
    }

    /**
     * @param int $topicId
     * @param string $studentUsername
     * @return void
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function insertStudent(int $topicId, string $studentUsername): void
    {
        $this->db->run(
            "INSERT INTO student_topics 
                   (student_id, topic_id, subject, last_rejected, teacher_comment, draft_path, subject_status)
                   VALUES (?, ?, NULL, NULL, NULL, NULL, NULL)",
            "si",
            $studentUsername,
            $topicId
        );
    }
}
