<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Drafts\Interfaces;

interface StudentDraftsManagerInterface
{
    public function update(string $studentUsername, string $teacherUsername, string $topicName): bool;
    public function getPath(string $studentUsername, string $teacherUsername, string $topicName): string;
}
