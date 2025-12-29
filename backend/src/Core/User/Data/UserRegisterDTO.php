<?php

namespace Goralys\Core\User\Data;

/**
 * The DTO used to register user
 */
class UserRegisterDTO
{
    private string $username;
    private string $fullName;
    private string $password;

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
