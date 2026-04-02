<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Subjects\Data;

use DateTime;

readonly class SpecialityDTO
{
    public function __construct(
        public string $teacherName,
        public string $speciality,
        public string $topicCode,
        public string $subject,
        public DateTime $validatedAt,
        public bool $interdisciplinary
    ) {
    }
}
