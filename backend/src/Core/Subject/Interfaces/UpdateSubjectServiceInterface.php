<?php

namespace Goralys\Core\Subject\Interfaces;

use Goralys\Core\Subject\Data\Enums\SubjectStatus;

interface UpdateSubjectServiceInterface
{
    public function updateSubject(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        string $newSubject
    ): bool;
    public function updateComment(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        string $newComment
    ): bool;
    public function updateSubjectStatus(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        SubjectStatus $newStatus
    ): bool;
}
