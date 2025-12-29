<?php

namespace Goralys\Core\Subject\Data;

use Goralys\App\Subjects\Services\SubjectsUsernameManager;
use Goralys\Core\Subject\Data\Enums\SubjectStatus;
use JsonSerializable;

/**
 * The DTO used to transport the data of a subject.
 * It is used inside the App and Core layers to get the subjects from the database.
 */
class SubjectDTO implements JsonSerializable
{
    private string $studentUsername;
    private string $studentUsernameToken;
    private string $subject;
    private SubjectStatus $status;
    private string $comment;
    private string $teacherUsername;
    private string $teacherUsernameToken;
    private string $topic;
    private string $lastRejected;

    public function __construct(
        string $studentUsername,
        string $studentUsernameToken,
        string $subject,
        SubjectStatus $status,
        string $comment,
        string $lastRejected,
        string $topic,
        string $teacherUsername,
        string $teacherUsernameToken,
    ) {
        $this->studentUsername = $studentUsername;
        $this->studentUsernameToken = $studentUsernameToken;
        $this->subject = $subject;
        $this->status = $status;
        $this->comment = $comment;
        $this->teacherUsername = $teacherUsername;
        $this->teacherUsernameToken = $teacherUsernameToken;
        $this->topic = $topic;
        $this->lastRejected = $lastRejected;
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
        ];
    }
}
