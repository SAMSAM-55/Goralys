<?php

namespace Goralys\Platform\DB\Data;

class StmtDto
{
    private string $query;
    private string $types;
    private array $args;

    public function __construct(
        string $query,
        string $types,
        mixed ...$args
    ) {
        $this->query = $query;
        $this->types = $types;
        $this->args = $args;
    }

    /**
     * Return the query for the statement
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Return the types of the statement's parameters
     * @return string
     */
    public function getTypes(): string
    {
        return $this->types;
    }

    /**
     * Return the statement's parameters
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
