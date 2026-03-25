<?php

namespace Goralys\Core\Subjects\Data;

class StudentSubjectsDTO
{
    public readonly string $studentName;
    /** @var SpecialityDTO[] */
    private array $subjects;

    public function __construct(string $studentName)
    {
        $this->studentName = $studentName;
        $this->subjects = [];
    }

    public function addSubject(SpecialityDTO $subject): void
    {
        $this->subjects[] = $subject;
    }

    /**
     * @return SpecialityDTO[]
     */
    public function getSubjects(): array
    {
        return $this->subjects;
    }
}
