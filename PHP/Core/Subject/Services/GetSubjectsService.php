<?php

namespace Goralys\Core\Subject\Services;

use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use Goralys\Core\Subject\Data\SubjectDTO;
use Goralys\Core\Subject\Data\SubjectsCollection;
use Goralys\Core\Subject\Interfaces\GetSubjectsServiceInterface;
use Goralys\Core\Subject\Repo\SubjectsRepository;
use Goralys\Core\Utils\User\Services\UsernameFormatterService;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use mysqli_result;

class GetSubjectsService implements GetSubjectsServiceInterface
{
    private GoralysLogger $logger;
    private SubjectsRepository $repo;
    private UsernameFormatterService $formatter;

    public function __construct(
        GoralysLogger $logger,
        SubjectsRepository $repo,
        UsernameFormatterService $formatter
    ) {
        $this->logger = $logger;
        $this->repo = $repo;
        $this->formatter = $formatter;
    }

    private function formatStudentSubjects(mysqli_result $result, string $studentUsername): SubjectsCollection
    {
        $subjects = new SubjectsCollection();

        while ($row = $result->fetch_assoc()) {
            $subject = new SubjectDTO(
                $this->formatter->formatUsername($studentUsername),
                $row['subject'],
                SubjectStatus::from($row['subject_status']),
                $row['comment'],
                $row['topic'],
                $this->formatter->formatUsername($row['teacher'])
            );

            $subjects->addSubject($subject);
        }

        return $subjects;
    }

    private function formatTeacherSubjects(mysqli_result $result, string $teacherUsername): SubjectsCollection
    {
        $subjects = new SubjectsCollection();

        while ($row = $result->fetch_assoc()) {
            $subject = new SubjectDTO(
                $this->formatter->formatUsername($row['student']),
                $row['subject'],
                SubjectStatus::from($row['subject_status']),
                $row['comment'],
                $row['topic'],
                $this->formatter->formatUsername($teacherUsername)
            );

            $subjects->addSubject($subject);
        }

        return $subjects;
    }

    private function formatAllSubjects(mysqli_result $result): SubjectsCollection
    {
        $subjects = new SubjectsCollection();

        while ($row = $result->fetch_assoc()) {
            $subject = new SubjectDTO(
                $this->formatter->formatUsername($row['student']),
                $row['subject'],
                SubjectStatus::from($row['subject_status']),
                $row['comment'] ?? "",
                $row['topic'],
                $this->formatter->formatUsername($row['teacher'])
            );

            $subjects->addSubject($subject);
        }

        return $subjects;
    }

    /**
     * @param string $studentUsername
     * @return SubjectsCollection
     * @throws GoralysPrepareException | GoralysQueryException
     */
    public function getStudentSubjects(string $studentUsername): SubjectsCollection
    {

        $result = $this->repo->findByStudent($studentUsername);

        $this->logger->info(
            LoggerInitiator::CORE,
            "Successfully fetched the subjects for student : " . $studentUsername
        );

        return $this->formatStudentSubjects($result, $studentUsername);
    }

    /**
     * @param string $teacherUsername
     * @return SubjectsCollection
     * @throws GoralysPrepareException | GoralysQueryException
     * */
    public function getTeacherSubjects(string $teacherUsername): SubjectsCollection
    {
        $result = $this->repo->findByTeacher($teacherUsername);

        $this->logger->info(
            LoggerInitiator::CORE,
            "Successfully fetched the subjects for teacher : " . $teacherUsername
        );

        return $this->formatTeacherSubjects($result, $teacherUsername);
    }

    /**
     * @return SubjectsCollection
     * @throws GoralysPrepareException | GoralysQueryException
     */
    public function getAllSubjects(): SubjectsCollection
    {
        $result = $this->repo->findAll();

        $this->logger->info(
            LoggerInitiator::CORE,
            "Granted access to all subjects for user : " . $_SESSION['current_username']
        );

        return $this->formatAllSubjects($result);
    }
}
