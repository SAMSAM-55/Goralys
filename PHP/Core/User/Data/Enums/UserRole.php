<?php

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
        $str = strtolower(trim($str));

        if ($str == "admin") {
            return UserRole::ADMIN;
        }
        if ($str == "teacher") {
            return UserRole::TEACHER;
        }
        if ($str == "student") {
            return UserRole::STUDENT;
        }
        return UserRole::UNKNOWN;
    }
}
