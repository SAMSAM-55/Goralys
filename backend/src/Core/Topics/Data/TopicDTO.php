<?php

namespace Goralys\Core\Topics\Data;

class TopicDTO
{
    private int $id;
    private string $name;
    private string $code;
    /* @var string[] */
    private array $teachers;
    /* @var string[] */
    private array $students;

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
