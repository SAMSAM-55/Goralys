<?php

namespace Goralys\Core\Drafts\Services;

use DirectoryIterator;
use Goralys\App\HTTP\Files\Interface\GoralysFileManagerInterface;
use Goralys\Core\Drafts\Interfaces\StudentDraftsManagerInterface;
use Goralys\Core\Subjects\Repository\Interfaces\SubjectsRepositoryInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\GoralysRuntimeException;

class StudentDraftsManager implements StudentDraftsManagerInterface
{
    private LoggerInterface $logger;
    private SubjectsRepositoryInterface $repo;
    private GoralysFileManagerInterface $fileManager;

    public function __construct(
        LoggerInterface $logger,
        SubjectsRepositoryInterface $repo,
        GoralysFileManagerInterface $fileManager
    ) {
        $this->logger = $logger;
        $this->repo = $repo;
        $this->fileManager = $fileManager;
    }

    private function emptyDir(string $studentUsername, string $teacherUsername): void
    {
        $fullDir = __DIR__ . "/../../../../Assets/StudentsDrafts/$teacherUsername/$studentUsername/";
        $teacherDir = __DIR__ . "/../../../../Assets/StudentsDrafts/$teacherUsername/";

        if (is_dir($fullDir)) {
            foreach (new DirectoryIterator($fullDir) as $file) {
                if (
                    $file->isFile()
                    && pathinfo($file->getFilename(), PATHINFO_FILENAME) === "draft"
                    && in_array(pathinfo($file->getFilename(), PATHINFO_EXTENSION), ['txt', 'odt', 'docx'])
                ) {
                    unlink($file->getPathname());
                }
            }
        } elseif (!is_dir($teacherDir)) {
            mkdir($teacherDir);
            mkdir($fullDir);
        } elseif (!is_dir($fullDir)) {
            mkdir($fullDir);
        }
    }

    /**
     * @param string $studentUsername
     * @param string $teacherUsername
     * @param string $topicName
     * @return bool
     */
    public function update(string $studentUsername, string $teacherUsername, string $topicName): bool
    {
        $uploadDir = __DIR__ . "/../../../../Assets/StudentsDrafts/$teacherUsername/$studentUsername/";
        $this->emptyDir($studentUsername, $teacherUsername);

        $file = $this->fileManager->get("draft-file");

        $extension = pathinfo($file->name, PATHINFO_EXTENSION) ?? "";
        $destination = $uploadDir . "draft." . $extension;

        if ($this->fileManager->move('draft-file', $destination)) {
            $this->logger->debug(
                LoggerInitiator::CORE,
                "Successfully moved uploaded draft for student: " . $studentUsername . ", with topic: " . $topicName
            );

            return $this->repo->updateDraftPath(
                $teacherUsername,
                $studentUsername,
                $topicName,
                $destination
            );
        }

        $this->logger->debug(
            LoggerInitiator::CORE,
            "Failed to move uploaded draft for student: " . $studentUsername . ", with topic: " . $topicName
                . " from: " . $file->tmpPath . ", to: " . $destination
        );

        return false;
    }

    /**
     * Retrieves the path to a student's draft
     * @param string $studentUsername
     * @param string $teacherUsername
     * @param string $topicName
     * @return string
     * @throws GoralysRuntimeException
     */
    public function getPath(string $studentUsername, string $teacherUsername, string $topicName): string
    {
        $result = $this->repo->getDraftPath($teacherUsername, $studentUsername, $topicName);
        if ($result->num_rows === 0) {
            throw new GoralysRuntimeException("Trying to access draft path for invalid teacher: " . $teacherUsername
            . ", student: " . $studentUsername . ", and topic: " . $topicName . " combination");
        }

        return $result->fetch_assoc()["path"];
    }
}
