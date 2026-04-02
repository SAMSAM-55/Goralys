<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Subjects\Services;

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Random\RandomException;

class SubjectsUsernameManager
{
    private LoggerInterface $logger;

    /**
     * Initializes the logger used by the service.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Creates a new entry in the lookup table for usernames.
     * This lookup table is used to avoid sending usernames to the frontend.
     * The keys of the table are randomly generated tokens.
     * The values of the table are the usernames.
     * As this table might be generated multiple times, it can contain some duplicates as values.
     * @param string $username The username to append to the lookup table.
     * @return string The token linked to the username.
     */
    public function store(string $username): string
    {
        try {
            $token = bin2hex(random_bytes(4));

            $_SESSION["username-table"][$token] = $username;
            return $token;
        } catch (RandomException $e) {
            $this->logger->warning(
                LoggerInitiator::APP,
                "Failed to generate user table token.\nError: " . $e->getMessage()
            );
        }

        return "";
    }

    /**
     * Retrieves a username from its token inside the usernames lookup table.
     * @param string $key The token associated with the username.
     * @return string The username.
     */
    public function get(string $key): string
    {
        return $_SESSION["username-table"][$key];
    }
}
