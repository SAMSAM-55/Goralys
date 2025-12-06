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

class UpdateSubjectService implements UpdateSubjectServiceInterface
{
    private GoralysLogger $logger;
    private SubjectsRepository $repo;

    public function __construct(
        GoralysLogger $logger,
        SubjectsRepository $repo
    ) {
        $this->logger = $logger;
        $this->repo = $repo;
    }

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
     * @param string $teacherUsername
     * @param string $studentUsername
     * @param string $newSubject
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
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
     * @param string $teacherUsername
     * @param string $studentUsername
     * @param string $newComment
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
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
     * Update the status of a given subject
     * @param string $teacherUsername
     * @param string $studentUsername
     * @param SubjectStatus $newStatus The new status of the subject
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
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
