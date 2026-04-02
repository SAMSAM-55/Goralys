<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Subjects\Services;

use Goralys\Core\Subjects\Data\Enums\SubjectStatus;
use Goralys\Core\Subjects\Repository\Interfaces\SubjectsRepositoryInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;

/**
 * The service used to update the subjects info inside the database via the subjects repository
 */
class UpdateSubjectService
{
    private LoggerInterface $logger;
    private SubjectsRepositoryInterface $repo;

    /**
     * Initializes the logger and the repository used by the service
     * @param LoggerInterface $logger The injected logger
     * @param SubjectsRepositoryInterface $repo The injected repository
     */
    public function __construct(
        LoggerInterface $logger,
        SubjectsRepositoryInterface $repo
    ) {
        $this->logger = $logger;
        $this->repo = $repo;
    }

    /**
     * This helper is used as a sort of macro to handle a result when trying to update a subject's field (property).
     * The service delegates the logging to this macro.
     * @param bool $result The result of the update request (true = success, false = fail).
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $field The field that the service tried to update.
     * @return void
     */
    private function handleResult(bool $result, string $teacherUsername, string $studentUsername, string $field): void
    {
        $pair = "($teacherUsername, $studentUsername)";

        if (!$result) {
            $this->logger->warning(
                LoggerInitiator::CORE,
                "Failed to update $field for (teacher, student) : $pair"
            );
            return;
        }

        $this->logger->info(
            LoggerInitiator::CORE,
            "Updated $field for (student, teacher) : $pair"
        );
    }

    /**
     * Update the content of a given subject.
     * A subject is always identified by a teacher and student combination.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $newSubject The subject's new content.
     * @param bool $interdisciplinary If the subject is interdisciplinary.
     * @return bool If the update was successful or not.
     */
    public function updateSubject(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        string $newSubject,
        bool $interdisciplinary
    ): bool {
        $result = $this->repo->updateSubject(
            $teacherUsername,
            $studentUsername,
            $topic,
            $newSubject,
            $interdisciplinary
        );

        $this->handleResult($result, $teacherUsername, $studentUsername, "subject");
        return $result;
    }

    /**
     * Update the comment of a given subject.
     * The comment is written by the teacher and visible for the teacher and the student.
     * It acts as feedback to guide the student.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param string $newComment The new teacher's comment about the subject.
     * @return bool If the update was successful or not.
     */
    public function updateComment(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        string $newComment
    ): bool {
        $result = $this->repo->updateComment($teacherUsername, $studentUsername, $topic, $newComment);

        $this->handleResult($result, $teacherUsername, $studentUsername, "comment");
        return $result;
    }


    /**
     * Update the status of a given subject.
     * @param string $teacherUsername The teacher's username.
     * @param string $studentUsername The student's username.
     * @param SubjectStatus $newStatus The new status of the subject.
     * @return bool If the update was successful or not.
     */
    public function updateSubjectStatus(
        string $teacherUsername,
        string $studentUsername,
        string $topic,
        SubjectStatus $newStatus
    ): bool {
        $result = $this->repo->updateStatus($teacherUsername, $studentUsername, $topic, $newStatus);

        $this->handleResult($result, $teacherUsername, $studentUsername, "status");
        return $result;
    }
}
