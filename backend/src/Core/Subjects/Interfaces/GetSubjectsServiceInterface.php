<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Subjects\Interfaces;

use Goralys\Core\Subjects\Data\SubjectsCollection;

interface GetSubjectsServiceInterface
{
    public function getStudentSubjects(string $studentUsername): SubjectsCollection;
    public function getTeacherSubjects(string $teacherUsername): SubjectsCollection;
    public function getAllSubjects(): SubjectsCollection;
}
