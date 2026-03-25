<?php

namespace Goralys\Core\Subjects\Data;

readonly class SpecialityDTO
{
    public string $teacherName;
    public string $speciality;
    public string $subject;
    public string $topicCode;

    public function __construct(
        string $teacherName,
        string $speciality,
        string $topicCode,
        string $subject
    ) {
        $this->teacherName = $teacherName;
        $this->speciality = $speciality;
        $this->topicCode = $topicCode;
        $this->subject = $subject;
    }
}
