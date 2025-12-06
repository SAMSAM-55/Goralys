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

class SubjectsController implements SubjectsControllerInterface
{
    private GoralysLogger $logger;
    private DbContainer $db;
    private SubjectsRepository $repo;
    private UpdateSubjectService $updateService;
    private UsernameFormatterService $formatter;
    private GetSubjectsService $getService;

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
     * @param string $teacherUsername
     * @param string $studentUsername
     * @param SubjectFields $field
     * @param string|SubjectStatus $newValue
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
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
            ),
            default => false,
        };
    }

    /**
     * @param UserRole $role
     * @param string $username
     * @return false|SubjectsCollection
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function getForRole(UserRole $role, string $username = ""): SubjectsCollection|false
    {
        return match ($role) {
            UserRole::STUDENT => $this->getService->getStudentSubjects($username),
            UserRole::TEACHER => $this->getService->getTeacherSubjects($username),
            UserRole::ADMIN => $this->getService->getAllSubjects(),
            default => false,
        };
    }
}
