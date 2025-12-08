<?php

namespace Goralys\App\Subjects\Controllers;

use Goralys\App\Subjects\Data\Enums\SubjectFields;
use Goralys\App\Subjects\Interfaces\SubjectsControllerInterface;
use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use Goralys\Core\Subject\Data\SubjectsCollection;
use Goralys\Core\Subject\Repo\SubjectsRepository;
use Goralys\Core\Subject\Services\GetSubjectsService;
use Goralys\Core\Subject\Services\UpdateSubjectService;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\Utils\User\Services\UsernameFormatterService;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

/**
 * The controller used to update/get subjects from the database via the `SubjectsRepository` (and intermediate services)
 */
class SubjectsController implements SubjectsControllerInterface
{
    private GoralysLogger $logger;
    private DbContainer $db;
    private SubjectsRepository $repo;
    private UpdateSubjectService $updateService;
    private UsernameFormatterService $formatter;
    private GetSubjectsService $getService;

    /**
     * Initializes the logger and database container for the controller.
     * Also instantiates all of its sub-services.
     * @param GoralysLogger $logger
     * @param DbContainer $db
     */
    public function __construct(
        GoralysLogger $logger,
        DbContainer $db
    ) {
        $this->logger = $logger;
        $this->db = $db;

        $this->repo = new SubjectsRepository($this->db);
        $this->formatter = new UsernameFormatterService();
        $this->updateService = new UpdateSubjectService($this->logger, $this->repo);
        $this->getService = new GetSubjectsService($this->logger, $this->repo, $this->formatter);
    }

    /**
     * Update a given field for teacher and student pair.
     * @param string $teacherUsername The username of the teacher.
     * @param string $studentUsername The username of the student.
     * @param SubjectFields $field The field to update.
     * @param string|SubjectStatus $newValue The new value of the field.
     * @return bool If the update was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the database request goes wrong.
     */
    public function updateField(
        string $teacherUsername,
        string $studentUsername,
        SubjectFields $field,
        string|SubjectStatus $newValue
    ): bool {
        return match ($field) {
            SubjectFields::SUBJECT => $this->updateService->updateSubject(
                $teacherUsername,
                $studentUsername,
                $newValue
            ),
            SubjectFields::STATUS => $this->updateService->updateSubjectStatus(
                $teacherUsername,
                $studentUsername,
                $newValue
            ),
            SubjectFields::COMMENT => $this->updateService->updateComment(
                $teacherUsername,
                $studentUsername,
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
        return match ($role) {
            UserRole::STUDENT => $this->getService->getStudentSubjects($username),
            UserRole::TEACHER => $this->getService->getTeacherSubjects($username),
            UserRole::ADMIN => $this->getService->getAllSubjects(),
            UserRole::UNKNOWN => false
        };
    }
}
