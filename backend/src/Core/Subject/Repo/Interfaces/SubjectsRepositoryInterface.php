<?php

namespace Goralys\Core\Subject\Repo\Interfaces;

use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use Goralys\Core\Subject\Data\SubjectDTO;
use mysqli_result;

interface SubjectsRepositoryInterface
{
    // Queries
    public function findByStudent(string $studentUsername): mysqli_result;
    public function findByTeacher(string $teacherUsername): mysqli_result;
    public function findAll(): mysqli_result;
    public function getStatus(string $teacherUsername, string $studentUsername, string $topic): mysqli_result;

    // Updates
    public function updateSubject(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        string $newSubject
    ): bool;
    public function updateStatus(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        SubjectStatus $newStatus
    ): bool;
    public function updateComment(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        string $newComment
    ): bool;
}
