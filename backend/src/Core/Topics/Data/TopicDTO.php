<?php

namespace Goralys\Core\Topics\Data;

/**
 * Data Transfer Object representing a Topic.
 */
class TopicDTO
{
    /** @var int The unique ID of the topic. */
    private int $id;
    /** @var string The name of the topic. */
    private string $name;
    /** @var string The unique code for the topic. */
    private string $code;
    /** @var string[] List of teacher usernames. */
    private array $teachers;
    /** @var string[] List of student usernames. */
    private array $students;

    /**
     * @param int $id
     * @param string $name
     * @param string $code
     * @param string[] $teachers
     * @param string[] $students
     */
    public function __construct(int $id, string $name, string $code, array $teachers, array $students)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->teachers = $teachers;
        $this->students = $students;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string[]
     */
    public function getTeachers(): array
    {
        return $this->teachers;
    }

    /**
     * @return string[]
     */
    public function getStudents(): array
    {
        return $this->students;
    }
}
