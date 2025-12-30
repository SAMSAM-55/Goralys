<?php

namespace Goralys\Core\Subject\Repo;

use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use Goralys\Core\Subject\Repo\Interfaces\SubjectsRepositoryInterface;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use mysqli_result;

/**
 * The repository to fetch and modify subjects inside the database.
 */
class SubjectsRepository implements SubjectsRepositoryInterface
{
    private DbContainer $db;

    /**
     * Initializes the database container for the repository.
     * @param DbContainer $db
     */
    public function __construct(
        DbContainer $db
    ) {
        $this->db = $db;
    }

    /**
     * Gets all the subjects for a given student.
     * @param string $studentUsername The student's username.
     * @return mysqli_result The result of the request.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function findByStudent(string $studentUsername): mysqli_result
    {
        return $this->db->fetch(
            "SELECT st.subject, st.subject_status, st.teacher_comment AS comment, st.last_rejected,
            t.name AS topic, t.teacher_id AS teacher
            FROM saje5795_goralys.student_topics st
            JOIN saje5795_goralys.topics t on t.id = st.topic_id
            WHERE st.student_id = ?",
            "s",
            $studentUsername
        );
    }

    /**
     * Gets all the subjects for a given teacher.
     * @param string $teacherUsername The teacher's username.
     * @return mysqli_result The result of the request.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     * */
    public function findByTeacher(string $teacherUsername): mysqli_result
    {
        return $this->db->fetch(
            "SELECT st.student_id AS student, st.subject, st.subject_status, st.teacher_comment AS comment, 
            st.last_rejected, t.name AS topic
            FROM saje5795_goralys.topics t
            JOIN saje5795_goralys.student_topics st on t.id = st.topic_id
            WHERE t.teacher_id = ?",
            "s",
            $teacherUsername
        );
    }

    /**
     * Gets all the subjects from the database.
     * This should only be used for admin accounts.
     * @return mysqli_result The result of the request.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function findAll(): mysqli_result
    {
        return $this->db->fetchNoArgs(
            "SELECT st.student_id AS student, st.subject, st.subject_status, st.teacher_comment AS comment, 
            st.last_rejected, t.name AS topic, t.teacher_id AS teacher
            FROM saje5795_goralys.topics t
            JOIN saje5795_goralys.student_topics st on t.id = st.topic_id"
        );
    }

    /**
     * Get the status of a given subject
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $topic The name of the topic.
     * @return mysqli_result The result of the request.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function getStatus(string $teacherUsername, string $studentUsername, string $topic): mysqli_result
    {
        return $this->db->fetch(
            "SELECT st.subject_status AS status
            FROM saje5795_goralys.topics t
            JOIN saje5795_goralys.student_topics st on t.id = st.topic_id
            WHERE t.teacher_id = ?
            AND st.student_id = ?
            AND t.name = ?",
            "sss",
            $teacherUsername,
            $studentUsername,
            $topic
        );
    }

    /**
     * Update a subject's content inside the database.
     * A subject is always identified by the combination of three variables: the teacher, the student, and the topic.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $topic The name of the topic.
     * @param string $newSubject The new subject.
     * @return bool If the update was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function updateSubject(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        string $newSubject
    ): bool {
        return $this->db->run(
            "UPDATE saje5795_goralys.student_topics st
            JOIN saje5795_goralys.topics t on t.id = st.topic_id
            SET st.subject = ?, st.subject_status = 0
            WHERE t.teacher_id = ?
            AND st.student_id = ?
            AND t.name = ?
            AND (st.subject_status = 0 OR st.subject_status = 2)",
            "ssss",
            $newSubject,
            $teacherUsername,
            $studentUsername,
            $topic
        );
    }

    /**
     * Update a subject's status inside the database.
     * A subject is always identified by the combination of three variables: the teacher, the student, and the topic.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $topic The name of the topic.
     * @param SubjectStatus $newStatus The new status of the subject.
     * @return bool If the update was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function updateStatus(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        SubjectStatus $newStatus
    ): bool {
        return $this->db->run(
            "UPDATE saje5795_goralys.student_topics st
            JOIN saje5795_goralys.topics t on t.id = st.topic_id
            SET st.subject_status = ?,
                st.last_rejected = IF(? = 2, st.subject, st.last_rejected)
            WHERE t.teacher_id = ?
            AND st.student_id = ?
            AND t.name = ?",
            "iisss",
            $newStatus->value,
            $newStatus->value,
            $teacherUsername,
            $studentUsername,
            $topic
        );
    }

    /**
     * Update a subject's comment inside the database.
     * The comment is written by the teacher and seen by both the teacher and the student.
     * A subject is always identified by the combination of three variables: the teacher, the student, and the topic.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $topic The name of the topic.
     * @param string$newComment The new comment for the subject.
     * @return bool If the update was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function updateComment(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        string $newComment
    ): bool {
        return $this->db->run(
            "UPDATE saje5795_goralys.student_topics st
            JOIN saje5795_goralys.topics t on t.id = st.topic_id
            SET st.teacher_comment = ?
            WHERE t.teacher_id = ?
            AND st.student_id = ?
            AND t.name = ?",
            "ssss",
            $newComment,
            $teacherUsername,
            $studentUsername,
            $topic
        );
    }
}
