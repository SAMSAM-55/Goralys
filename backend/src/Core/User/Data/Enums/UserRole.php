<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Data\Enums;

enum UserRole: int
{
    case ADMIN = 2;
    case TEACHER = 1;
    case STUDENT = 0;
    case UNKNOWN = -1;

    public function toString(): string
    {
        return strtolower($this->name);
    }

    public static function fromString(string $str): UserRole
    {
        return match (strtolower(trim($str))) {
            "admin" => UserRole::ADMIN,
            "teacher" => UserRole::TEACHER,
            "student" => UserRole::STUDENT,
            default => UserRole::UNKNOWN
        };
    }

    public function isAtLeast(UserRole $reference): bool
    {
        return $this->value >= $reference->value;
    }
}
