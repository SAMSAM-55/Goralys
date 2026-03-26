<?php

namespace Goralys\App\Subjects\Controllers;

use DateTime;
use Goralys\App\HTTP\Files\Interface\GoralysFileManagerInterface;
use Goralys\App\Subjects\Data\Enums\SubjectFields;
use Goralys\App\Subjects\Interfaces\SubjectsControllerInterface;
use Goralys\App\Subjects\Services\SubjectsUsernameManager;
use Goralys\Core\Drafts\Services\StudentDraftsManager;
use Goralys\Core\Subjects\Config\SubjectsExportConfig;
use Goralys\Core\Subjects\Data\Enums\SubjectStatus;
use Goralys\Core\Subjects\Data\SpecialityDTO;
use Goralys\Core\Subjects\Data\StudentSubjectsDTO;
use Goralys\Core\Subjects\Data\SubjectsCollection;
use Goralys\Core\Subjects\Data\SubjectDTO;
use Goralys\Core\Subjects\Repository\SubjectsRepository;
use Goralys\Core\Subjects\Services\GetSubjectsService;
use Goralys\Core\Subjects\Services\SubjectsTemplateRenderer;
use Goralys\Core\Subjects\Services\UpdateSubjectService;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Repository\UserRepository;
use Goralys\Core\Utils\User\Services\UsernameFormatterService;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Platform\Doc\PDF\Interfaces\PdfExporterInterface;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use Goralys\Shared\Exception\GoralysRuntimeException;
use ZipArchive;

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
    private UserRepository $userRepo;
    private SubjectsTemplateRenderer $renderer;
    private SubjectsExportConfig $exportConfig;
    private PdfExporterInterface $exporter;

    /**
     * Initializes the logger and database container for the controller.
     * Also instantiates all of its sub-services.
     * @param LoggerInterface $logger The injected logger.
     * @param DbContainer $db The injected db.
     * @param GoralysFileManagerInterface $fileManager The injected file manager.
     * @param PdfExporterInterface $exporter The injected PDF exporter.
     */
    public function __construct(
        LoggerInterface $logger,
        DbContainer $db,
        GoralysFileManagerInterface $fileManager,
        PdfExporterInterface $exporter
    ) {
        $this->logger = $logger;
        $this->db = $db;

        $this->repo = new SubjectsRepository($this->db);
        $this->userRepo = new UserRepository($this->logger, $this->db);
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
        $this->exportConfig = new SubjectsExportConfig();
        $this->exporter = $exporter;
        $this->renderer = new SubjectsTemplateRenderer($exporter, $this->exportConfig);
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
     * @return SubjectsCollection The list of the retrieved subjects.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the database request goes wrong.
     */
    public function getForRole(UserRole $role, string $username = ""): SubjectsCollection
    {
        unset($_SESSION['username-table']);

        return match ($role) {
            UserRole::STUDENT => $this->getService->getStudentSubjects($username),
            UserRole::TEACHER => $this->getService->getTeacherSubjects($username),
            UserRole::ADMIN => $this->getService->getAllSubjects(),
            UserRole::UNKNOWN => new SubjectsCollection()
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

    /**
     * @param SubjectsCollection $subjects
     * @return StudentSubjectsDTO[]
     * @throws GoralysPrepareException|GoralysQueryException
     */
    private function groupByStudents(SubjectsCollection $subjects): array
    {
        /** @var SubjectDTO[][] $grouped */
        $grouped = [];
        foreach ($subjects->getSubjects() as $subject) {
            $grouped[$this->usernameManager->get($subject->studentUsernameToken)][] = $subject;
        }

        return array_values(array_map(
        /**
         * @param string $username
         * @param SubjectDTO[] $subjects
         * @return StudentSubjectsDTO
         * @throws GoralysPrepareException
         * @throws GoralysQueryException
         */
            function (string $username, array $subjects) {
                $dto = new StudentSubjectsDTO(
                    $this->userRepo->getFullNameForUsername($username)
                );

                foreach ($subjects as $subject) {
                    $dto->addSubject(new SpecialityDTO(
                        $this->userRepo->getFullNameForUsername(
                            $this->usernameManager->get($subject->teacherUsernameToken)
                        ),
                        $subject->topic,
                        $subject->topicCode,
                        $subject->subject,
                        $subject->lastUpdatedAt ?? new DateTime()
                    ));
                }

                return $dto;
            },
            array_keys($grouped),
            $grouped
        ));
    }

    /**
     * @param SubjectsCollection $subjects
     * @return string The path to the generated zip file.
     * @throws GoralysPrepareException|GoralysQueryException
     * @throws GoralysRuntimeException
     */
    public function exportAll(SubjectsCollection $subjects): string
    {
        $grouped = $this->groupByStudents($subjects);
        $exportedPaths = [];

        foreach ($grouped as $s) {
            $filename = $this->exportConfig::EXPORT_BASE_NAME . date("Y") . " - " . $s->studentName . ".pdf";
            $filePath = $this->exportConfig::ASSETS_PATH . "Exports/" . $filename;

            $pdf = $this->renderer->render($s);
            $this->exporter->export(
                $pdf,
                $filePath,
                $this->exportConfig::ASSETS_PATH
            );

            $exportedPaths[] = $filePath;
        }

        return $this->zipExports($exportedPaths);
    }

    /**
     * Deletes all exported files (PDFs and zips) from the exports directory.
     * @return void
     * @throws GoralysRuntimeException If the exports directory does not exist or a file cannot be deleted.
     */
    public function cleanExports(): void
    {
        $exportsDir = $this->exportConfig::ASSETS_PATH . "Exports/";

        if (!is_dir($exportsDir)) {
            throw new GoralysRuntimeException("Exports directory not found at: $exportsDir");
        }

        $files = array_merge(glob($exportsDir . "*.pdf"), glob($exportsDir . "*.zip"));

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            if (!unlink($file)) {
                throw new GoralysRuntimeException("Failed to delete export file: $file");
            }
        }
    }

    /**
     * Zips a list of exported PDF files into a single archive.
     * @param string[] $filePaths The list of PDF file paths to zip.
     * @return string The path to the generated zip file.
     * @throws GoralysRuntimeException If the zip archive could not be created or a file is missing.
     */
    private function zipExports(array $filePaths): string
    {
        $zipPath = $this->exportConfig::ASSETS_PATH . "Exports/export_" . date("Y-m-d_His") . ".zip";

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new GoralysRuntimeException("Could not create zip archive at: $zipPath");
        }

        foreach ($filePaths as $path) {
            if (!file_exists($path)) {
                $zip->close();
                throw new GoralysRuntimeException("File not found when zipping: $path");
            }
            $zip->addFile($path, basename($path));
        }

        $zip->close();

        return $zipPath;
    }
}
