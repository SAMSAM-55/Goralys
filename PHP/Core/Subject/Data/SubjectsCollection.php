<?php

namespace Goralys\Core\Subject\Data;

use Goralys\Core\Subject\Data\SubjectDTO;
use JsonSerializable;

class SubjectsCollection implements JsonSerializable
{
    /* @var SubjectDTO[] */
    private array $subjects;

    public function addSubject(SubjectDTO $newSubject): void
    {
        $this->subjects[] = $newSubject;
    }

    public function jsonSerialize(): array
    {
        return $this->subjects;
    }
}
