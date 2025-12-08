<?php

namespace Goralys\Core\Subject\Data\Enums;

enum SubjectStatus: int
{
    case UNKNOWN = -1;
    case NOT_SUBMITTED = 0;
    case SUBMITTED = 1;
    case REJECTED = 2;
    case APPROVED = 3;

    /**
     * @return string
     */
    public function toString(): string
    {
        return strtolower($this->name);
    }

    /**
     * @param string $str
     * @return SubjectStatus
     */
    public static function fromString(string $str): SubjectStatus
    {
        $str = trim(strtolower($str));

        if ($str === "not_submitted") {
            return SubjectStatus::NOT_SUBMITTED;
        }
        if ($str === "submitted") {
            return SubjectStatus::SUBMITTED;
        }
        if ($str === "rejected") {
            return SubjectStatus::REJECTED;
        }
        if ($str === "approved") {
            return SubjectStatus::APPROVED;
        }
        return SubjectStatus::UNKNOWN;
    }
}
