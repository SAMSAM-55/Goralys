<?php

namespace Goralys\App\Subjects\Interfaces;

use Goralys\Core\Subjects\Data\Enums\SubjectStatus;
use Goralys\Core\Subjects\Data\SubjectsCollection;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\App\Subjects\Data\Enums\SubjectFields;

interface SubjectsControllerInterface
{
    public function updateField(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        SubjectFields $field,
        string|SubjectStatus $newValue
    ): bool;
    public function getStatus(string $teacherUsername, string $studentUsername, string $topic): SubjectStatus;
    public function getForRole(UserRole $role, string $username = ""): ?SubjectsCollection;
    public function exportAll(SubjectsCollection $subjects): string;
    public function cleanExports(): void;
}
