<?php

namespace Goralys\Core\Subject\Data;

use JsonSerializable;

/**
 * A special object used to represent an array of subjects.
 */
class SubjectsCollection implements JsonSerializable
{
    /* @var SubjectDTO[] */
    private array $subjects;

    /**
     * Adds a new subject to the collection.
     * @param SubjectDTO $newSubject The subject to add.
     * @return void
     */
    public function addSubject(SubjectDTO $newSubject): void
    {
        $this->subjects[] = $newSubject;
    }

    /**
     * Transforms the subjects collection into a JSON array
     * @return SubjectDTO[]
     */
    public function jsonSerialize(): array
    {
        return $this->subjects;
    }
}
