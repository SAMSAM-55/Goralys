<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Subjects\Data;

use DateTime;
use Goralys\Core\Subjects\Data\Enums\SubjectStatus;
use JsonSerializable;

/**
 * The DTO used to transport the data of a subject.
 * It is used inside the App and Core layers to get the subjects from the database.
 */
readonly class SubjectDTO implements JsonSerializable
{
    public function __construct(
        public string $studentUsername,
        public string $studentUsernameToken,
        public string $subject,
        public SubjectStatus $status,
        public string $comment,
        public string $lastRejected,
        public ?DateTime $lastUpdatedAt,
        public string $topic,
        public string $topicCode,
        public string $teacherUsername,
        public string $teacherUsernameToken,
        public bool $interdisciplinary,
        public bool $hasDraft = false
    ) {
    }

    /**
     * Transforms the subject's data into a JSON object that is then sent to the frontend
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
                "student" => $this->studentUsername,
                "studentToken" => $this->studentUsernameToken,
                "subject" => $this->subject,
                "status" => $this->status->toString(),
                "comment" => $this->comment,
                "lastRejected" => $this->lastRejected,
                "topic" => $this->topic,
                "teacher" => $this->teacherUsername,
                "teacherToken" => $this->teacherUsernameToken,
                "hasDraft" => $this->hasDraft,
                "interdisciplinary" => $this->interdisciplinary
        ];
    }
}
