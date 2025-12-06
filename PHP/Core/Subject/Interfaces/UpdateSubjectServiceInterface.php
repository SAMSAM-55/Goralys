<?php

namespace Goralys\Core\Subject\Interfaces;

use Goralys\Core\Subject\Data\Enums\SubjectStatus;

interface UpdateSubjectServiceInterface
{
    public function updateSubject(
        string $teacherUsername,
        string $studentUsername,
        string $newSubject
    ): bool;
    public function updateComment(
        string $teacherUsername,
        string $studentUsername,
        string $newComment
    ): bool;
    public function updateSubjectStatus(
        string $teacherUsername,
        string $studentUsername,
        SubjectStatus $newStatus
    ): bool;
}
