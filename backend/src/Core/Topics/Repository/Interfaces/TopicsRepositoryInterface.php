<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Topics\Repository\Interfaces;

interface TopicsRepositoryInterface
{
    public function insertTopic(int $topicId, string $topicCode, string $topicName): bool;
    public function insertTeacher(int $topicId, string $teacherUsername): bool;
    public function insertStudent(int $topicId, string $studentUsername): bool;
    public function clearAll(): bool;
}
