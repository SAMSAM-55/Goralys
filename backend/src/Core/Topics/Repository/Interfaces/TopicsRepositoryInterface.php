<?php

namespace Goralys\Core\Topics\Repository\Interfaces;

use Goralys\Core\Topics\Data\TopicDTO;

interface TopicsRepositoryInterface
{
    public function insertTopic(int $topicId, string $topicCode, string $topicName): void;
    public function insertTeacher(int $topicId, string $teacherUsername): void;
    public function insertStudent(int $topicId, string $studentUsername): void;
}
