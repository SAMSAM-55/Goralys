<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Data\Enums;

/**
 * Represents the role of a user in the application.
 * Roles are ordered by privilege level: ADMIN > TEACHER > STUDENT > UNKNOWN.
 */
enum UserRole: int
{
    case ADMIN = 2;
    case TEACHER = 1;
    case STUDENT = 0;
    case UNKNOWN = -1;

    /**
     * Returns the lowercase name of the role as a string.
     * @return string The role name in lowercase.
     */
    public function toString(): string
    {
        return strtolower($this->name);
    }

    /**
     * Creates a UserRole from a string representation.
     * Returns UNKNOWN if the string does not match any known role.
     * @param string $str The role string to convert.
     * @return UserRole The matching role.
     */
    public static function fromString(string $str): UserRole
    {
        return match (strtolower(trim($str))) {
            "admin" => UserRole::ADMIN,
            "teacher" => UserRole::TEACHER,
            "student" => UserRole::STUDENT,
            default => UserRole::UNKNOWN
        };
    }

    /**
     * Checks whether this role has at least the same privilege level as the given reference role.
     * @param UserRole $reference The minimum required role.
     * @return bool True if this role's level is greater than or equal to the reference.
     */
    public function isAtLeast(UserRole $reference): bool
    {
        return $this->value >= $reference->value;
    }
}
