<?php

namespace Goralys\Core\Subject\Data;

use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use JsonSerializable;

class SubjectDTO implements JsonSerializable
{
    private string $studentUsername;
    private string $subject;
    private SubjectStatus $status;
    private string $comment;
    private string $teacherUsername;
    private string $topic;

    public function __construct(
        string $studentUsername,
        string $subject,
        SubjectStatus $status,
        string $comment,
        string $topic,
        string $teacherUsername
    ) {
        $this->studentUsername = $studentUsername;
        $this->subject = $subject;
        $this->status = $status;
        $this->comment = $comment;
        $this->teacherUsername = $teacherUsername;
        $this->topic = $topic;
    }

    /**
     * @return string
     */
    public function getStudentUsername(): string
    {
        return $this->studentUsername;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return SubjectStatus
     */
    public function getStatus(): SubjectStatus
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getTeacherUsername(): string
    {
        return $this->teacherUsername;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
                "student" => $this->studentUsername,
                "subject" => $this->subject,
                "status" => $this->status->toString(),
                "comment" => $this->comment,
                "topic" => $this->topic,
                "teacher" => $this->teacherUsername
        ];
    }
}
