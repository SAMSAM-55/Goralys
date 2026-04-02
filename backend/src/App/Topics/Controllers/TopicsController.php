<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Topics\Controllers;

use Goralys\App\HTTP\Files\Data\UploadedFileDTO;
use Goralys\App\HTTP\Files\GoralysFileManager;
use Goralys\Core\Topics\Config\TopicsImportConfig;
use Goralys\Core\Topics\Data\TopicDescriptorDTO;
use Goralys\Core\Topics\Data\TopicDTO;
use Goralys\Core\Topics\Repository\Interfaces\TopicsRepositoryInterface;
use Goralys\Core\Topics\Repository\TopicsRepository;
use Goralys\Core\Topics\Services\BuildFromCSVService;
use Goralys\Platform\DB\Interfaces\DbContainerInterface;
use Goralys\Shared\Exception\GoralysRuntimeException;
use Goralys\Shared\Utils\String\Data\StringCase;
use Goralys\Shared\Utils\UtilitiesManager;

/**
 * Controller for managing topic-related operations, including CSV/ZIP imports and persistence.
 */
class TopicsController
{
    /** @var UtilitiesManager Utility manager for string operations and more. */
    private UtilitiesManager $utils;
    /** @var DbContainerInterface Database container instance. */
    private DbContainerInterface $db;
    /** @var TopicsImportConfig Configuration for topic imports. */
    private TopicsImportConfig $config;
    /** @var TopicsRepositoryInterface Repository for topic database operations. */
    private TopicsRepositoryInterface $repo;
    /** @var BuildFromCSVService Service to build topics from CSV data. */
    private BuildFromCSVService $CSVBuilder;
    /** @var GoralysFileManager Manager for file operations. */
    private GoralysFileManager $files;
    /** @var array<string, string> Cache for username mappings (Full Name => username). */
    private array $usernameTable; // Temporary will be moved soon after the testing phase
    /** @var int Internal ID generator for topics during import. */
    private int $nextId;

    /**
     * @param DbContainerInterface $db
     * @param UtilitiesManager $utils
     * @param GoralysFileManager $files
     */
    public function __construct(
        DbContainerInterface $db,
        UtilitiesManager $utils,
        GoralysFileManager $files
    ) {
        $this->usernameTable = [];
        $this->utils = $utils;

        $this->db = $db;
        $this->config = new TopicsImportConfig();

        $this->repo = new TopicsRepository($this->db);
        $this->CSVBuilder = new BuildFromCSVService($this->utils, $this->config);
        $this->nextId = 0;
        $this->files = $files;
    }

    /**
     * Creates a new TopicDTO instance.
     *
     * @param string $name The name of the topic.
     * @param string $code The code (ID) of the topic.
     * @param string[] $students A list of student names or usernames.
     * @param string[] $teachers A list of teacher names or usernames.
     * @return TopicDTO
     */
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

    /**
     * Generates a unique username based on a person's full name.
     *
     * @param string $fullName The full name of the student or teacher.
     * @return string The generated username (e.g., 'f.lastname9').
     */
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
     * Inserts a TopicDTO into the database, including its students and teachers.
     *
     * @param TopicDTO $topic The topic data transfer object to insert.
     */
    public function insert(TopicDTO $topic): void
    {
        $this->repo->insertTopic(
            $topic->id,
            $topic->code,
            $topic->name
        );

        foreach ($topic->teachers as $t) {
            $this->repo->insertTeacher(
                $topic->id,
                $this->generateUsername($t)
            );
        }

        foreach ($topic->students as $s) {
            $this->repo->insertStudent(
                $topic->id,
                $this->generateUsername($s)
            );
        }
    }

    /**
     * Creates a temporary extraction directory for a ZIP file.
     *
     * @param UploadedFileDTO $file The uploaded file metadata.
     * @return string The absolute path to the temporary directory.
     */
    private function makeExtractionDir(UploadedFileDTO $file): string
    {
        $baseDir = dirname($file->tmpPath);
        return $baseDir . DIRECTORY_SEPARATOR . "extracted_" . uniqid('', true) . DIRECTORY_SEPARATOR;
    }

    /**
     * Loads the group-to-teacher mapping from the 'groupes.csv' file in the extraction directory.
     *
     * @param string $dir The path to the extraction directory.
     * @return array<string, list<string>> A mapping of group codes to teacher usernames.
     * @throws GoralysRuntimeException If the groups mapping file is missing.
     */
    private function loadGroupsMapping(string $dir): array
    {
        $groupsCsv = $dir . $this->config::GROUPS_FILENAME;
        if (!is_file($groupsCsv)) {
            throw new GoralysRuntimeException("Missing groups mapping file in ZIP (expected at: $groupsCsv)");
        }

        return $this->CSVBuilder->buildGroups($groupsCsv);
    }

    /**
     * Finds all CSV files in the extraction directory that are not the groups mapping file.
     *
     * @param string $dir The extraction directory path.
     * @return string[] List of absolute paths to topic CSV files.
     */
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

    /**
     * Parses a topic CSV filename to extract its code and name.
     * Expected format: CODE_Name.csv
     *
     * @param string $filePath The full path to the CSV file.
     * @return TopicDescriptorDTO|null The parsed descriptor or null if invalid.
     */
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
     * Processes an uploaded ZIP file and creates TopicDTO objects from its content.
     *
     * @param UploadedFileDTO $file The ZIP file metadata.
     * @return TopicDTO[] A list of constructed TopicDTO objects.
     * @throws GoralysRuntimeException If no valid topic files are found.
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
            $teachers = $groupsToTeachers[$descriptor->code] ?? [];

            $topics[] = $this->makeTopic($descriptor->name, $descriptor->code, $students, $teachers);
        }

        if (count($topics) === 0) {
            throw new GoralysRuntimeException("No topic CSV files found in ZIP (expected files like CODE_Name.csv).");
        }

        return $topics;
    }

    /**
     * Writes all usernames associated with the full name of the user into a file.
     * @param TopicDTO[] $topics The topics to export the usernames for.
     * @return string The path to the file where the topics where exported.
     */
    public function exportUsernames(array $topics): string
    {
        $out = "";
        foreach ($topics as $topic) {
            $head = "--------------- " . $topic->code . ": " . $topic->name . " ---------------";
            $out .= $head . PHP_EOL;

            $out .= "Professeurs:" . PHP_EOL;
            foreach ($topic->teachers as $teacher) {
                $out .= "    - " . $teacher . ": " . $this->usernameTable[$teacher] . PHP_EOL;
            }

            $out .= "Elèves:" . PHP_EOL;
            foreach ($topic->students as $student) {
                $out .= "    - " . $student . ": " . $this->usernameTable[$student] . PHP_EOL;
            }

            $out .= str_repeat("-", strlen($head)) . PHP_EOL;
        }

        $path = tempnam(sys_get_temp_dir(), "goralys_");
        file_put_contents($path, $out);

        return $path;
    }

    /**
     * Removes all topics and associated subjects from the database.
     * @return bool If the deletion was successful
     */
    public function clear(): bool
    {
        return $this->repo->clearAll();
    }
}
