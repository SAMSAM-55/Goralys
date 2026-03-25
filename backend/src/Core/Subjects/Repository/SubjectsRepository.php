<?php

namespace Goralys\Core\Subjects\Repository;

use Goralys\Core\Subjects\Data\Enums\SubjectStatus;
use Goralys\Core\Subjects\Repository\Interfaces\SubjectsRepositoryInterface;
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
    public function __construct(DbContainer $db)
    {
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
            "select
                st.subject,
                st.subject_status,
                st.teacher_comment as comment,
                st.last_rejected,
                t.name as topic,
                t.topic_code as topic_code,
                GROUP_CONCAT(distinct tt.teacher_id order by tt.teacher_id separator ', ') as teachers
            from student_topics st
            join topics t on t.id = st.topic_id
            join topic_teachers tt on t.id = tt.topic_id
            where st.student_id = ?
            group by st.student_id, st.topic_id",
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
            "select
                st.student_id as student,
                st.subject,
                st.subject_status,
                st.teacher_comment as comment,
                st.last_rejected,
                t.name as topic,
                t.topic_code as topic_code,
                st.draft_path as draftPath,
                GROUP_CONCAT(distinct tt.teacher_id order by tt.teacher_id separator ', ') as teachers
            from topics t
            join student_topics st on t.id = st.topic_id
            join topic_teachers tt on t.id = tt.topic_id
            where tt.teacher_id = ?
            group by st.student_id, st.topic_id",
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
            "select
                st.student_id as student,
                st.subject,
                st.subject_status,
                st.teacher_comment as comment,
                st.last_rejected,
                t.name as topic,
                t.topic_code as topic_code,
                GROUP_CONCAT(distinct tt.teacher_id order by tt.teacher_id separator ', ') as teachers
            from topics t
            join topic_teachers tt on t.id = tt.topic_id
            join student_topics st on t.id = st.topic_id
            group by st.student_id, st.topic_id"
        );
    }

    /**
     * Get the status of a given subject.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $topic The name of the topic.
     * @return mysqli_result The result of the request.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function getStatus(string $teacherUsername, string $studentUsername, string $topic): mysqli_result
    {
        return $this->db->fetch(
            "select st.subject_status as status
            from topics t
            join student_topics st on t.id = st.topic_id
            join topic_teachers tt on t.id = tt.topic_id
            where tt.teacher_id = ?
              and st.student_id = ?
              and t.name = ?",
            "sss",
            $teacherUsername,
            $studentUsername,
            $topic
        );
    }

    /**
     * Get the path to a student's draft for a given subject.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $topic The name of the topic.
     * @return mysqli_result The result of the request.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function getDraftPath(string $teacherUsername, string $studentUsername, string $topic): mysqli_result
    {
        return $this->db->fetch(
            "select st.draft_path as path
            from topics t
            join student_topics st on t.id = st.topic_id
            join topic_teachers tt on t.id = tt.topic_id
            where tt.teacher_id = ?
              and st.student_id = ?
              and t.name = ?",
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
            "update student_topics st
            join topics t on t.id = st.topic_id
            join topic_teachers tt on t.id = tt.topic_id
            set st.subject = ?, st.subject_status = 0
            where tt.teacher_id = ?
            and st.student_id = ?
            and t.name = ?
            and (st.subject_status = 0 or st.subject_status = 2)",
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
            "update student_topics st
            join topics t on t.id = st.topic_id
            join topic_teachers tt on t.id = tt.topic_id
            set st.subject_status = ?,
                st.last_rejected = IF(? = 2, st.subject, st.last_rejected)
            where tt.teacher_id = ?
            and st.student_id = ?
            and t.name = ?",
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
     * @param string $newComment The new comment for the subject.
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
            "update student_topics st
            join topics t on t.id = st.topic_id
            join topic_teachers tt on t.id = tt.topic_id
            set st.teacher_comment = ?
            where tt.teacher_id = ?
            and st.student_id = ?
            and t.name = ?",
            "ssss",
            $newComment,
            $teacherUsername,
            $studentUsername,
            $topic
        );
    }

    /**
     * Update a subject's draft path inside the database.
     * The comment is written by the teacher and seen by both the teacher and the student.
     * A subject is always identified by the combination of three variables: the teacher, the student, and the topic.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $topic The name of the topic.
     * @param string$newPath The new path to the student's draft.
     * @return bool If the update was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function updateDraftPath(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        string $newPath
    ): bool {
        return $this->db->run(
            "update student_topics st
            join topics t on t.id = st.topic_id
            join topic_teachers tt on t.id = tt.topic_id
            set st.draft_path = ?
            where tt.teacher_id = ?
            and st.student_id = ?
            and t.name = ?",
            "ssss",
            $newPath,
            $teacherUsername,
            $studentUsername,
            $topic
        );
    }
}
