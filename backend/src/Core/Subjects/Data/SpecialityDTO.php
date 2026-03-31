<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Subjects\Data;

use DateTime;

readonly class SpecialityDTO
{
    public string $teacherName;
    public string $speciality;
    public string $subject;
    public string $topicCode;
    public DateTime $validatedAt;

    public function __construct(
        string $teacherName,
        string $speciality,
        string $topicCode,
        string $subject,
        DateTime $validatedAt
    ) {
        $this->teacherName = $teacherName;
        $this->speciality = $speciality;
        $this->topicCode = $topicCode;
        $this->subject = $subject;
        $this->validatedAt = $validatedAt;
    }
}
