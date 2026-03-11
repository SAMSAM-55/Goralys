<?php

namespace Goralys\Core\Subjects\Interfaces;

use Goralys\Core\Subjects\Data\SubjectsCollection;

interface GetSubjectsServiceInterface
{
    public function getStudentSubjects(string $studentUsername): SubjectsCollection;
    public function getTeacherSubjects(string $teacherUsername): SubjectsCollection;
    public function getAllSubjects(): SubjectsCollection;
}
