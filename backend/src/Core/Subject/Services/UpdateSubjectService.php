<?php

namespace Goralys\Core\Subject\Services;

use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use Goralys\Core\Subject\Interfaces\UpdateSubjectServiceInterface;
use Goralys\Core\Subject\Repo\SubjectsRepository;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use mysqli_result;

/**
 * The service used to update the subjects info inside the database via the subjects repository
 */
class UpdateSubjectService implements UpdateSubjectServiceInterface
{
    private GoralysLogger $logger;
    private SubjectsRepository $repo;

    /**
     * Initializes the logger and the repository used by the service
     * @param GoralysLogger $logger The injected logger
     * @param SubjectsRepository $repo The injected repository
     */
    public function __construct(
        GoralysLogger $logger,
        SubjectsRepository $repo
    ) {
        $this->logger = $logger;
        $this->repo = $repo;
    }

    /**
     * This helper is used as a sort of macro to handle a result when trying to update a subject's field (property).
     * The service delegates the logging to this macro.
     * @param bool $result The result of the update request (true = success, false = fail).
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $field The field that the service tried to update.
     * @return void
     */
    private function handleResult(bool $result, string $teacherUsername, string $studentUsername, string $field): void
    {
        $pair = "($teacherUsername, $studentUsername)";

        if (!$result) {
            $this->logger->warning(
                LoggerInitiator::CORE,
                "Failed to update $field for (teacher, student) : $pair"
            );
            return;
        }

        $this->logger->info(
            LoggerInitiator::CORE,
            "Updated $field for (student, teacher) : $pair"
        );
    }

    /**
     * Update the content of a given subject.
     * A subject is always identified by a teacher and student combination.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $newSubject The subject's new content.
     * @return bool If the update was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function updateSubject(
        string $teacherUsername,
        string $studentUsername,
        string $newSubject
    ): bool {
        $result = $this->repo->updateSubject($teacherUsername, $studentUsername, $newSubject);

        $this->handleResult($result, $teacherUsername, $studentUsername, "subject");
        return $result;
    }

    /**
     * Update the comment of a given subject.
     * The comment is written by the teacher and visible for the teacher and the student.
     * It acts as a feedback to guide the student.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $newComment The new teacher's comment about the subject.
     * @return bool If the update was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function updateComment(
        string $teacherUsername,
        string $studentUsername,
        string $newComment
    ): bool {
        $result = $this->repo->updateComment($teacherUsername, $studentUsername, $newComment);

        $this->handleResult($result, $teacherUsername, $studentUsername, "comment");
        return $result;
    }


    /**
     * Update the status of a given subject.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param SubjectStatus $newStatus The new status of the subject.
     * @return bool If the update was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function updateSubjectStatus(
        string $teacherUsername,
        string $studentUsername,
        SubjectStatus $newStatus
    ): bool {
        $result = $this->repo->updateStatus($teacherUsername, $studentUsername, $newStatus);

        $this->handleResult($result, $teacherUsername, $studentUsername, "status");
        return $result;
    }
}
