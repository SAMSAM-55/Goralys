<?php

namespace Goralys\Platform\DB\Data;

/**
 * The DTO used to transport the data of a statement across the different services and layers.
 * Its use stops as soon as the statement is prepared (it is now a `mysqli_stmt`).
 */
class StmtDto
{
    private string $query;
    private string $types;
    private array $args;

    public function __construct(
        string $query,
        string $types,
        mixed $value1,
        mixed ...$args
    ) {
        $this->query = $query;
        $this->types = $types;
        $this->args = array_merge([$value1], $args);
    }

    /**
     * Return the query for the statement.
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Return the types of the statement's parameters.
     * @return string
     */
    public function getTypes(): string
    {
        return $this->types;
    }

    /**
     * Return the statement's parameters.
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
