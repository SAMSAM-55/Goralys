<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\User\Data;

use Goralys\Shared\Utils\String\Data\StringCase;
use Goralys\Shared\Utils\UtilitiesManager;

final class UsernameTable
{
    /** @var array<string, string> */
    private array $table = [];

    public function __construct(private readonly UtilitiesManager $utils) {}

    /**
     * Returns the username for a full name, generating and caching it if needed.
     */
    public function resolve(string $fullName): string
    {
        if (isset($this->table[$fullName])) {
            return $this->table[$fullName];
        }

        $fullName = trim($fullName);
        $names = explode(" ", $fullName);
        [$lastName, $firstName] = array_slice($names, -2);
        $firstName = $this->utils->string->sanitize($firstName, StringCase::LOWER);
        $lastName = $this->utils->string->sanitize(explode("-", $lastName)[0], StringCase::LOWER);
        $number = rand(0, 9);

        return $this->table[$fullName] = substr($firstName, 0, 1) . "." . $lastName . $number;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->table;
    }
}
