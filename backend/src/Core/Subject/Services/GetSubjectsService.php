<?php

namespace Goralys\Core\Subject\Services;

use Goralys\App\Subjects\Services\SubjectsUsernameManager;
use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use Goralys\Core\Subject\Data\SubjectDTO;
use Goralys\Core\Subject\Data\SubjectsCollection;
use Goralys\Core\Subject\Interfaces\GetSubjectsServiceInterface;
use Goralys\Core\Subject\Repo\Interfaces\SubjectsRepositoryInterface;
use Goralys\Core\Utils\User\Services\UsernameFormatterService;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use mysqli_result;

/**
 * The service used to fetch subjects inside the database via the subjects repository
 */
class GetSubjectsService implements GetSubjectsServiceInterface
{
    private LoggerInterface $logger;
    private SubjectsRepositoryInterface $repo;
    private UsernameFormatterService $formatter;
    private SubjectsUsernameManager $usernameManager;

    /**
     * Initializes the logger, database container and a utility service - the username formatter - used by the service.
     * @param LoggerInterface $logger The injected logger
     * @param SubjectsRepositoryInterface $repo The injected subjects repository
     * @param UsernameFormatterService $formatter The injected username formatter
     * This formatter is used to transform the usernames stored inside the database with the format f.lastnameX to
     * lastname. f with f the first letter of the first name.
     * This is useful to avoid displaying private usernames to other users.
     * Therefore, as the username format is very specific to the project, you may need to tweak the username formatter
     * or get rid of it completely depending on your project.
     */
    public function __construct(
        LoggerInterface $logger,
        SubjectsRepositoryInterface $repo,
        UsernameFormatterService $formatter,
        SubjectsUsernameManager $usernameManager,
    ) {
        $this->logger = $logger;
        $this->repo = $repo;
        $this->formatter = $formatter;
        $this->usernameManager = $usernameManager;
    }

    /**
     * A simple helper to transform a request result from the repository into a usable subjects collection.
     * This version is specific to student subjects.
     * There are three declinations of this function for students, teachers and admins (all) as the formatting varies
     * from one role to another as the inputs are different.
     * @param mysqli_result $result The result of the request from the repository.
     * @param string $studentUsername The student's username.
     * @return SubjectsCollection The array containing all the subjects in a more usable format.
     * The `SubjectsCollection` is also used here as it implements a custom way to transform it into a JSON array and
     * thus make the output process ot the frontend more straightforward.
     */
    private function formatStudentSubjects(mysqli_result $result, string $studentUsername): SubjectsCollection
    {
        $subjects = new SubjectsCollection();

        while ($row = $result->fetch_assoc()) {
            $subject = new SubjectDTO(
                $this->formatter->formatUsername($studentUsername),
                $this->usernameManager->store($studentUsername),
                $row['subject'] ?? "",
                SubjectStatus::from($row['subject_status']),
                $row['comment'] ?? "",
                $row['last_rejected'] ?? "",
                $row['topic'],
                $this->formatter->formatUsername($row['teacher']),
                $this->usernameManager->store($row['teacher']),
            );

            $subjects->addSubject($subject);
        }

        return $subjects;
    }

    /**
     * A simple helper to transform a request result from the repository into a usable subjects collection.
     * This version is specific to teacher subjects.
     * There are three declinations of this function for students, teachers and admins (all) as the formatting varies
     * from one role to another as the inputs are different.
     * @param mysqli_result $result The result of the request from the repository.
     * @param string $teacherUsername The teacher's username.
     * @return SubjectsCollection The array containing all the subjects in a more usable format.
     * The `SubjectsCollection` is also used here as it implements a custom way to transform it into a JSON array and
     * thus make the output process ot the frontend more straightforward.
     */
    private function formatTeacherSubjects(mysqli_result $result, string $teacherUsername): SubjectsCollection
    {
        $subjects = new SubjectsCollection();

        while ($row = $result->fetch_assoc()) {
            $subject = new SubjectDTO(
                $this->formatter->formatUsername($row['student']),
                $this->usernameManager->store($row['student']),
                $row['subject'],
                SubjectStatus::from($row['subject_status']),
                $row['comment'] ?? "",
                $row['last_rejected'] ?? "",
                $row['topic'],
                $this->formatter->formatUsername($teacherUsername),
                $this->usernameManager->store($teacherUsername),
            );

            $subjects->addSubject($subject);
        }

        return $subjects;
    }

    /**
     * A simple helper to transform a request result from the repository into a usable subjects collection.
     * This version is specific to admin accounts and thus is made to format all the subjects at so it doesn't
     * require a username.
     * There are three declinations of this function for students, teachers and admins (all) as the formatting varies
     * from one role to another as the inputs are different.
     * @param mysqli_result $result The result of the request from the repository
     * @return SubjectsCollection The array containing all the subjects in a more usable format.
     * The `SubjectsCollection` is also used here as it implements a custom way to transform it into a JSON array and
     * thus make the output process to the frontend more straightforward.
     */
    private function formatAllSubjects(mysqli_result $result): SubjectsCollection
    {
        $subjects = new SubjectsCollection();

        while ($row = $result->fetch_assoc()) {
            $subject = new SubjectDTO(
                $this->formatter->formatUsername($row['student']),
                $this->usernameManager->store($row['student']),
                $row['subject'],
                SubjectStatus::from($row['subject_status']),
                $row['comment'] ?? "",
                $row['last_rejected'] ?? "",
                $row['topic'],
                $this->formatter->formatUsername($row['teacher']),
                $this->usernameManager->store($row['teacher']),
            );

            $subjects->addSubject($subject);
        }

        return $subjects;
    }

    /**
     * Gets all the subjects for a given student.
     * It uses the subjects repository to communicate with the database.
     * The subjects are returned using a subjects collection object.
     * @param string $studentUsername The student's username.
     * @return SubjectsCollection The array of all the student's subjects.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
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
     * Gets all the subjects for a given teacher.
     * It uses the subjects repository to communicate with the database.
     * The subjects are returned using a subjects collection object.
     * @param string $teacherUsername The teacher's username.
     * @return SubjectsCollection The array of all the teacher's subjects.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
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
     * Gets all the subjects thus it should only be used for admins.
     * It uses the subjects repository to communicate with the database.
     * The subjects are returned using a subjects collection object.
     * @return SubjectsCollection The array of all the subjects inside the database.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function getAllSubjects(): SubjectsCollection
    {
        $result = $this->repo->findAll();

        $this->logger->info(
            LoggerInitiator::CORE,
            "Granted access to all subjects for user : " . ($_SESSION['current_username'] ?? "")
        );

        return $this->formatAllSubjects($result);
    }
}
