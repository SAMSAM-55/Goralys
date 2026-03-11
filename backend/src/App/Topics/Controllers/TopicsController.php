<?php

namespace Goralys\App\Topics\Controllers;

use Goralys\App\HTTP\Files\Data\UploadedFileDTO;
use Goralys\App\HTTP\Files\GoralysFileManager;
use Goralys\App\HTTP\Files\Interface\FileExtractor;
use Goralys\App\Topics\Interfaces\TopicsControllerInterface;
use Goralys\Core\Topics\Config\TopicsImportConfig;
use Goralys\Core\Topics\Data\TopicDescriptorDTO;
use Goralys\Core\Topics\Data\TopicDTO;
use Goralys\Core\Topics\Repository\TopicsRepository;
use Goralys\Core\Topics\Services\BuildFromCSVService;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use Goralys\Shared\Exception\GoralysRuntimeException;
use Goralys\Shared\Utils\String\Data\StringCase;
use Goralys\Shared\Utils\UtilitiesManager;
use ZipArchive;

class TopicsController implements TopicsControllerInterface
{
    private UtilitiesManager $utils;
    private LoggerInterface $logger;
    private DbContainer $db;
    private TopicsImportConfig $config;
    private TopicsRepository $repository;
    private BuildFromCSVService $CSVBuilder;
    private GoralysFileManager $files;
    private array $usernameTable; // Temporary will be moved soon after the testing phase
    private int $nextId;

    public function __construct(
        LoggerInterface $logger,
        DbContainer $db,
        UtilitiesManager $utils,
        GoralysFileManager $files
    ) {
        $this->usernameTable = [];
        $this->utils = $utils;

        $this->logger = $logger;
        $this->db = $db;
        $this->config = new TopicsImportConfig();

        $this->repository = new TopicsRepository($this->db);
        $this->CSVBuilder = new BuildFromCSVService($this->utils, $this->config);
        $this->nextId = 0;
        $this->files = $files;
    }

    public function makeTopic(string $name, string $code, array $students, array $teachers): TopicDTO
    {
        $this->nextId++;
        return new TopicDTO(
            $this->nextId,
            $name,
            $code,
            $teachers,
            $students
        );
    }

    private function generateUsername(string $fullName): string
    {
        if (in_array($fullName, array_keys($this->usernameTable))) {
            return $this->usernameTable[$fullName];
        }

        $fullName = trim($fullName);
        $names = explode(" ", $fullName);
        [$lastName, $firstName] = array_slice($names, -2);
        $firstName = $this->utils->string->sanitize($firstName, StringCase::LOWER);
        $lastName = $this->utils->string->sanitize(explode("-", $lastName)[0], StringCase::LOWER);
        $number = rand(0, 9);

        $userName = substr($firstName, 0, 1) . "." . $lastName . $number;
        $this->usernameTable[$fullName] = $userName;
        return $this->usernameTable[$fullName];
    }

    /**
     * @throws GoralysQueryException|GoralysPrepareException
     */
    public function insert(TopicDTO $topic): void
    {
        $this->repository->insertTopic(
            $topic->getId(),
            $topic->getCode(),
            $topic->getName()
        );

        foreach ($topic->getTeachers() as $t) {
            $this->repository->insertTeacher(
                $topic->getId(),
                $this->generateUsername($t)
            );
        }

        foreach ($topic->getStudents() as $s) {
            $this->repository->insertStudent(
                $topic->getId(),
                $this->generateUsername($s)
            );
        }
    }

    private function makeExtractionDir(UploadedFileDTO $file): string
    {
        $baseDir = dirname($file->tmpPath);
        return $baseDir . DIRECTORY_SEPARATOR . "extracted_" . uniqid('', true) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $dir
     * @return array<string, list<string>>
     * @throws GoralysRuntimeException
     */
    private function loadGroupsMapping(string $dir): array
    {
        $groupsCsv = $dir . $this->config::GROUPS_FILENAME;
        if (!is_file($groupsCsv)) {
            throw new GoralysRuntimeException("Missing groups mapping file in ZIP (expected at: $groupsCsv)");
        }

        return $this->CSVBuilder->buildGroups($groupsCsv);
    }

    private function findTopicsCsv(string $dir): array
    {
        $pattern = $dir . "*.csv";
        $csvFiles = [];
        foreach (glob($pattern) as $csv) {
            if (strtolower($csv) == $this->config::GROUPS_FILENAME) {
                continue;
            }
            $csvFiles[] = $csv;
        }

        return $csvFiles;
    }

    private function parseTopicFilename(string $filePath): ?TopicDescriptorDTO
    {
        $base = basename($filePath);
        $filename = pathinfo($base, PATHINFO_FILENAME);
        $pos = strpos($filename, $this->config::TOPIC_CODE_NAME_SEPARATOR);

        if ($pos === false) {
            return null;
        }

        $code = trim(substr($filename, 0, $pos));
        $rawName = trim(substr($filename, $pos + 1));

        if ($code === '' || $rawName === '') {
            return null;
        }

        return new TopicDescriptorDTO($rawName, $code);
    }

    /**
     * @param UploadedFileDTO $file
     * @return TopicDTO[]
     * @throws GoralysRuntimeException
     */
    public function makeTopicsFromZip(UploadedFileDTO $file): array
    {
        $extractTo = $this->makeExtractionDir($file);
        $this->files->extract($file, $extractTo);

        $groupsToTeachers = $this->loadGroupsMapping($extractTo);

        $topics = [];

        foreach ($this->findTopicsCsv($extractTo) as $csvPath) {
            $descriptor = $this->parseTopicFilename($csvPath);
            if ($descriptor === null) {
                continue;
            }

            $students = $this->CSVBuilder->buildStudents($csvPath);
            $teachers = $groupsToTeachers[$descriptor->getCode()] ?? [];

            $topics[] = $this->makeTopic($descriptor->getName(), $descriptor->getCode(), $students, $teachers);
        }

        if (count($topics) === 0) {
            throw new GoralysRuntimeException("No topic CSV files found in ZIP (expected files like CODE_Name.csv).");
        }

        return $topics;
    }
}
