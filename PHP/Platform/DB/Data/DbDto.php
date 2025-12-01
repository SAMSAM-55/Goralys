<?php

namespace Goralys\Platform\DB\Data;

// Main Database DTO
// It's internal to the DB layer and contains the credentials to log in to the database
class DbDto
{
    private string $host;

    private string $name;
    private string $username;
    private string $password;

    public function __construct(
        string $host,
        string $name,
        string $username,
        string $password
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
