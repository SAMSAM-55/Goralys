<?php

namespace Goralys\Core\Subjects\Data;

use Goralys\App\Subjects\Services\SubjectsUsernameManager;
use DateTime;
use Goralys\Core\Subjects\Data\Enums\SubjectStatus;
use JsonSerializable;

/**
 * The DTO used to transport the data of a subject.
 * It is used inside the App and Core layers to get the subjects from the database.
 */
readonly class SubjectDTO implements JsonSerializable
{
    public string $studentUsername;
    public string $studentUsernameToken;
    public string $subject;
    public SubjectStatus $status;
    public string $comment;
    public string $teacherUsername;
    public string $teacherUsernameToken;
    public string $topic;
    public string $topicCode;
    public string $lastRejected;
    public ?DateTime $lastUpdatedAt;
    public bool $hasDraft;

    public function __construct(
        string $studentUsername,
        string $studentUsernameToken,
        string $subject,
        SubjectStatus $status,
        string $comment,
        string $lastRejected,
        ?DateTime $lastUpdatedAt,
        string $topic,
        string $topicCode,
        string $teacherUsername,
        string $teacherUsernameToken,
        bool $hasDraft = false
    ) {
        $this->studentUsername = $studentUsername;
        $this->studentUsernameToken = $studentUsernameToken;
        $this->subject = $subject;
        $this->status = $status;
        $this->comment = $comment;
        $this->teacherUsername = $teacherUsername;
        $this->teacherUsernameToken = $teacherUsernameToken;
        $this->topic = $topic;
        $this->topicCode = $topicCode;
        $this->lastRejected = $lastRejected;
        $this->lastUpdatedAt = $lastUpdatedAt;
        $this->hasDraft = $hasDraft;
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
                "hasDraft" => $this->hasDraft
        ];
    }
}
