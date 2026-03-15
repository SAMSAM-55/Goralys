<?php

namespace Goralys\Core\Topics\Repository;

use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

/**
 * Repository class for handling database operations related to Topics.
 */
class TopicsRepository implements Interfaces\TopicsRepositoryInterface
{
    /** @var DbContainer The database container. */
    private DbContainer $db;

    /**
     * @param DbContainer $db
     */
    public function __construct(
        DbContainer $db
    ) {
        $this->db = $db;
    }

    /**
     * Inserts a new topic into the 'topics' table.
     *
     * @param int $topicId The unique ID of the topic.
     * @param string $topicCode The unique code for the topic.
     * @param string $topicName The display name of the topic.
     * @return void
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function insertTopic(int $topicId, string $topicCode, string $topicName): void
    {
        $this->db->run(
            "insert into topics (id, topic_code, name) values (?, ?, ?)",
            "iss",
            $topicId,
            $topicCode,
            $topicName
        );
    }

    /**
     * Associates a teacher with a topic in the 'topic_teachers' table.
     *
     * @param int $topicId The ID of the topic.
     * @param string $teacherUsername The username of the teacher.
     * @return void
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function insertTeacher(int $topicId, string $teacherUsername): void
    {
        $this->db->run(
            "insert into topic_teachers (topic_id, teacher_id) values (?, ?)",
            "is",
            $topicId,
            $teacherUsername
        );
    }

    /**
     * Associates a student with a topic in the 'student_topics' table.
     *
     * @param int $topicId The ID of the topic.
     * @param string $studentUsername The username of the student.
     * @return void
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function insertStudent(int $topicId, string $studentUsername): void
    {
        $this->db->run(
            "insert into student_topics 
                   (student_id, topic_id, subject, last_rejected, teacher_comment, draft_path, subject_status)
                   values (?, ?, null, null, null, null, 0)",
            "si",
            $studentUsername,
            $topicId
        );
    }

    /**
     * Removes all topics and associated subjects from the database.
     * @return bool If the deletion was successful
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function clearAll(): bool
    {
        $this->db->runNoArgs("set FOREIGN_KEY_CHECKS = 0");
        $this->db->runNoArgs("delete from student_topics");
        $this->db->runNoArgs("delete from topic_teachers");
        $this->db->runNoArgs("delete from topics");
        $this->db->runNoArgs("set FOREIGN_KEY_CHECKS = 1");

        return true;
    }
}
