<?php

namespace Goralys\Core\Topics\Interfaces;

use Goralys\App\HTTP\Files\Data\UploadedFileDTO;

interface BuildFromCSVServiceInterface
{
    /* @return array<string, list<string>> */
    public function buildGroups(string $from): array;
    /* @return string[] */
    public function buildStudents(string $from): array;
}
