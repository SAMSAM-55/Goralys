<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\DB\Data;

/**
 * Main Database DTO.
 * It's internal to the DB layer and contains the credentials to log in to the database.
 */
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

    /**
     * Returns the username of the database connection
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Returns the password of the database connection
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Returns the host of the database
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Returns the name of the database
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
