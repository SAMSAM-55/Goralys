<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Topics\Repository\Interfaces;

use Goralys\Core\Topics\Data\TopicDTO;

interface TopicsRepositoryInterface
{
    public function insertTopic(int $topicId, string $topicCode, string $topicName): void;
    public function insertTeacher(int $topicId, string $teacherUsername): void;
    public function insertStudent(int $topicId, string $studentUsername): void;
    public function clearAll(): bool;
}
