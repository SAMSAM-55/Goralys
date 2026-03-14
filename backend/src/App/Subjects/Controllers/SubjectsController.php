<?php

namespace Goralys\App\Subjects\Controllers;

use Goralys\App\HTTP\Files\Interface\GoralysFileManagerInterface;
use Goralys\App\Subjects\Data\Enums\SubjectFields;
use Goralys\App\Subjects\Interfaces\SubjectsControllerInterface;
use Goralys\App\Subjects\Services\SubjectsUsernameManager;
use Goralys\Core\Drafts\Services\StudentDraftsManager;
use Goralys\Core\Subjects\Data\Enums\SubjectStatus;
use Goralys\Core\Subjects\Data\SubjectsCollection;
use Goralys\Core\Subjects\Repository\SubjectsRepository;
use Goralys\Core\Subjects\Services\GetSubjectsService;
use Goralys\Core\Subjects\Services\UpdateSubjectService;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\Utils\User\Services\UsernameFormatterService;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

/**
 * The controller used to update/get subjects from the database via the `SubjectsRepository` (and intermediate services)
 */
class SubjectsController implements SubjectsControllerInterface
{
    private LoggerInterface $logger;
    private DbContainer $db;
    private SubjectsRepository $repo;
    private UpdateSubjectService $updateService;
    private UsernameFormatterService $formatter;
    private SubjectsUsernameManager $usernameManager;
    public StudentDraftsManager $draftsManager;
    private GoralysFileManagerInterface $fileManager;
    private GetSubjectsService $getService;

    /**
     * Initializes the logger and database container for the controller.
     * Also instantiates all of its sub-services.
     * @param LoggerInterface $logger The injected logger.
     * @param DbContainer $db The injected db.
     * @param GoralysFileManagerInterface $fileManager The injected file manager.
     */
    public function __construct(
        LoggerInterface $logger,
        DbContainer $db,
        GoralysFileManagerInterface $fileManager
    ) {
        $this->logger = $logger;
        $this->db = $db;

        $this->repo = new SubjectsRepository($this->db);
        $this->formatter = new UsernameFormatterService();
        $this->usernameManager = new SubjectsUsernameManager($this->logger);
        $this->fileManager = $fileManager;
        $this->draftsManager = new StudentDraftsManager($this->logger, $this->repo, $this->fileManager);
        $this->updateService = new UpdateSubjectService($this->logger, $this->repo);
        $this->getService = new GetSubjectsService(
            $this->logger,
            $this->repo,
            $this->formatter,
            $this->usernameManager
        );
    }

    /**
     * Update a given field for a teacher and student pair.
     * @param string $teacherUsername The username of the teacher.
     * @param string $studentUsername The username of the student.
     * @param string $topic The name of the topic.
     * @param SubjectFields $field The field to update.
     * @param string|SubjectStatus $newValue The new value of the field.
     * @return bool If the update was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the database request goes wrong.
     */
    public function updateField(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        SubjectFields $field,
        string|SubjectStatus $newValue
    ): bool {
        return match ($field) {
            SubjectFields::SUBJECT => $this->updateService->updateSubject(
                $teacherUsername,
                $studentUsername,
                $topic,
                $newValue
            ),
            SubjectFields::STATUS => $this->updateService->updateSubjectStatus(
                $teacherUsername,
                $studentUsername,
                $topic,
                $newValue
            ),
            SubjectFields::COMMENT => $this->updateService->updateComment(
                $teacherUsername,
                $studentUsername,
                $topic,
                $newValue
            )
        };
    }

    /**
     * Get the subjects for a given user with a given role.
     * @param UserRole $role The role of the user to get the subjects of.
     * @param string $username The username of the student or teacher to get the subjects for.
     * Let the defaults value ("") for admins as they have access to all subjects.
     * @return false|SubjectsCollection The list of the retrieved subjects.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the database request goes wrong.
     */
    public function getForRole(UserRole $role, string $username = ""): SubjectsCollection|false
    {
        unset($_SESSION['username-table']);

        return match ($role) {
            UserRole::STUDENT => $this->getService->getStudentSubjects($username),
            UserRole::TEACHER => $this->getService->getTeacherSubjects($username),
            UserRole::ADMIN => $this->getService->getAllSubjects(),
            UserRole::UNKNOWN => false
        };
    }

    /**
     * Get the status of a given subject.
     * @param string $teacherUsername The username of the teacher.
     * @param string $studentUsername The username of the student.
     * @param string $topic The name of the topic.
     * @return SubjectStatus The status of the subject.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function getStatus(string $teacherUsername, string $studentUsername, string $topic): SubjectStatus
    {
        $result = $this->repo->getStatus($teacherUsername, $studentUsername, $topic);

        $status = $result->fetch_assoc()['status'];

        return SubjectStatus::from($status);
    }
}
