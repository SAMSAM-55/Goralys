<?php

namespace Goralys\Core\Subject\Data;

use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use JsonSerializable;

/**
 * The DTO used to transport the data of a subject.
 * It is used inside the App and Core layers to get the subjects from the database.
 */
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
     * Transforms the subject's data into a JSON object that is then sent to the frontend
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
