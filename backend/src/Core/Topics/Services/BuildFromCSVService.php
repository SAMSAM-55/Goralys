<?php

namespace Goralys\Core\Topics\Services;

use Goralys\Core\Topics\Config\TopicsImportConfig;
use Goralys\Core\Topics\Interfaces\BuildFromCSVServiceInterface;
use Goralys\Shared\Exception\GoralysRuntimeException;
use Goralys\Shared\Utils\UtilitiesManager;

class BuildFromCSVService implements BuildFromCSVServiceInterface
{
    private UtilitiesManager $utils;
    private TopicsImportConfig $config;

    public function __construct(UtilitiesManager $utils, TopicsImportConfig $config)
    {
        $this->utils = $utils;
        $this->config = $config;
    }

    /**
     * @throws GoralysRuntimeException
     */
    private function ensureCSV(string $path): \SplFileObject
    {
        if (!is_file($path) || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'csv') {
            throw new GoralysRuntimeException(
                "The provided file ($path) is not a valid CSV file."
            );
        }

        try {
            $file = new \SplFileObject($path, 'r');

            $file->setFlags(
                \SplFileObject::READ_CSV |
                \SplFileObject::SKIP_EMPTY |
                \SplFileObject::DROP_NEW_LINE
            );

            $file->setCsvControl(",", escape: '');

            return $file;
        } catch (\RuntimeException $e) {
            throw new GoralysRuntimeException(
                "Could not open CSV file ($path).",
                previous: $e
            );
        }
    }

    /**
     * @param string $from
     * @return array<string, list<string>>
     * @throws GoralysRuntimeException
     */
    public function buildGroups(string $from): array
    {
        $file = $this->ensureCSV($from);

        $result = [];

        foreach ($file as $i => $row) {
            if ($row === null || $row === false || $row === [null]) {
                continue;
            }

            if (count($row) !== 2) {
                throw new GoralysRuntimeException(
                    "CSV format error at line " . ($i + 1) . ": expected 2 columns."
                );
            }

            [$groupId, $teachersRaw] = $row;

            $groupId = trim($groupId);
            $teachersRaw = trim($teachersRaw);

            $teachers = array_map('trim', explode($this->config::TEACHERS_SEPARATOR, $teachersRaw));
            $teachers = array_values(array_filter($teachers, fn(string $t) => $t !== ''));

            $result[$groupId] = $teachers;
        }

        return $result;
    }

    /**
     * @param string $from
     * @return array
     * @throws GoralysRuntimeException
     */
    public function buildStudents(string $from): array
    {
        $file = $this->ensureCSV($from);

        $students = [];

        $firstRow = null;
        foreach ($file as $row) {
            if ($row === null || $row === false || $row === [null]) {
                continue;
            }
            $firstRow = $row;
            if (isset($firstRow[0])) {
                $firstRow[0] = ltrim($firstRow[0], "\xEF\xBB\xBF"); // Remove UTF-8 BOM
            }
            break;
        }

        if ($firstRow === null) {
            return [];
        }

        $normalized = array_map(
            fn($v) => $this->utils->string->sanitize((string) $v),
            $firstRow
        );

        $studentCol = null;
        foreach ($normalized as $idx => $name) {
            if (in_array(trim(strtolower($name)), ['eleve', 'élève', 'student', 'etudiant', 'étudiant', 'nom'], true)) {
                $studentCol = $idx;
                break;
            }
        }

        $hasHeader = ($studentCol !== null);

        if (!$hasHeader) {
            $studentCol = 0;

            $s = isset($firstRow[$studentCol]) ? trim((string) $firstRow[$studentCol]) : '';
            if ($s !== '' && mb_check_encoding($s, 'UTF-8') && !str_contains($s, "\u{FFFD}")) {
                $students[] = $s;
            }
        }

        $file->rewind();

        $headerSkipped = false;

        foreach ($file as $row) {
            if ($row === null || $row === false || $row === [null]) {
                continue;
            }

            if ($hasHeader && !$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            $s = isset($row[$studentCol]) ? trim((string) $row[$studentCol]) : '';
            if ($s === '') {
                continue;
            }

            $students[] = $s;
        }

        return array_values(array_unique($students));
    }
}
