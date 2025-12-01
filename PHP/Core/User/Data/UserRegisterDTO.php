<?php

namespace Goralys\Core\User\Data;

class UserRegisterDTO
{
    private readonly string $username;
    private readonly string $fullName;
    private readonly string $password;

    public function __construct(
        string $username,
        string $fullName,
        string $password
    ) {
        $this->username = $username;
        $this->fullName = $fullName;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
