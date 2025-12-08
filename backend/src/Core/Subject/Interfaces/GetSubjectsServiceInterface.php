<?php

namespace Goralys\Core\Subject\Interfaces;

use Goralys\Core\Subject\Data\SubjectsCollection;

interface GetSubjectsServiceInterface
{
    public function getStudentSubjects(string $studentUsername): SubjectsCollection;
    public function getTeacherSubjects(string $teacherUsername): SubjectsCollection;
    public function getAllSubjects(): SubjectsCollection;
}
