<?php

namespace Goralys\Core\Drafts\Interfaces;

interface StudentDraftsManagerInterface
{
    public function update(string $studentUsername, string $teacherUsername, string $topicName): bool;
    public function getPath(string $studentUsername, string $teacherUsername, string $topicName): string;
}
