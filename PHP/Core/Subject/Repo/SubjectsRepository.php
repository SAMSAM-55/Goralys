<?php

namespace Goralys\Core\Subject\Repo;

use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use Goralys\Core\Subject\Repo\Interfaces\SubjectsRepositoryInterface;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use mysqli_result;

class SubjectsRepository implements SubjectsRepositoryInterface
{
    private DbContainer $db;

    public function __construct(
        DbContainer $db
    ) {
        $this->db = $db;
    }

    /**
     * @param string $studentUsername
     * @return mysqli_result
     * @throws GoralysPrepareException | GoralysQueryException
     */
    public function findByStudent(string $studentUsername): mysqli_result
    {
        return $this->db->fetch(
            "SELECT st.subject, st.subject_status, st.teacher_comment AS comment, 
            t.name AS topic, t.teacher_id AS teacher
            FROM saje5795_goralys.student_topics st
            JOIN saje5795_goralys.topics t on t.id = st.topic_id
            WHERE st.student_id = ?",
            "s",
            $studentUsername
        );
    }

    /**
     * @param string $teacherUsername
     * @return mysqli_result
     * @throws GoralysPrepareException | GoralysQueryException
     * */
    public function findByTeacher(string $teacherUsername): mysqli_result
    {
        return $this->db->fetch(
            "SELECT st.student_id AS student, st.subject, st.subject_status, st.teacher_comment AS commment, 
            t.name AS topic
            FROM saje5795_goralys.topics t
            JOIN saje5795_goralys.student_topics st on t.id = st.topic_id
            WHERE t.teacher_id = ?",
            "s",
            $teacherUsername
        );
    }

    /**
     * @return mysqli_result
     * @throws GoralysPrepareException | GoralysQueryException
     */
    public function findAll(): mysqli_result
    {
        return $this->db->fetchNoArgs(
            "SELECT st.student_id AS student, st.subject, st.subject_status, st.teacher_comment AS comment, 
            t.name AS topic, t.teacher_id AS teacher
            FROM saje5795_goralys.topics t
            JOIN saje5795_goralys.student_topics st on t.id = st.topic_id"
        );
    }

    /**
     * @param string $teacherUsername
     * @param string $studentUsername
     * @param string $newSubject
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function updateSubject(string $teacherUsername, string $studentUsername, string $newSubject): bool
    {
        return $this->db->run(
            "UPDATE saje5795_goralys.student_topics st
            JOIN saje5795_goralys.topics t on t.id = st.topic_id
            SET st.subject_status = ?
            WHERE t.teacher_id = ?
            AND st.student_id = ?",
            "sss",
            $newSubject,
            $teacherUsername,
            $studentUsername
        );
    }

    /**
     * @param string $teacherUsername
     * @param string $studentUsername
     * @param SubjectStatus $newStatus
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function updateStatus(string $teacherUsername, string $studentUsername, SubjectStatus $newStatus): bool
    {
        return $this->db->run(
            "UPDATE saje5795_goralys.student_topics st
            JOIN saje5795_goralys.topics t on t.id = st.topic_id
            SET st.subject_status = ?
            WHERE t.teacher_id = ?
            AND st.student_id = ?",
            "iss",
            $newStatus->value,
            $teacherUsername,
            $studentUsername
        );
    }

    /**
     * @param string $teacherUsername
     * @param string $studentUsername
     * @param string $newComment
     * @return bool
     * @throws GoralysQueryException|GoralysPrepareException
     */
    public function updateComment(string $teacherUsername, string $studentUsername, string $newComment): bool
    {
        return $this->db->run(
            "UPDATE saje5795_goralys.student_topics st
            JOIN saje5795_goralys.topics t on t.id = st.topic_id
            SET st.teacher_comment = ?
            WHERE t.teacher_id = ?
            AND st.student_id = ?",
            "sss",
            $newComment,
            $teacherUsername,
            $studentUsername
        );
    }
}
