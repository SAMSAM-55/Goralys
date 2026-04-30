<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Subjects\Data\Enums;

/**
 * Represents the fields of a subject that can be individually updated.
 */
enum SubjectFields: string
{
    case SUBJECT = "subject";
    case STATUS = "status";
    case COMMENT = "comment";
}
