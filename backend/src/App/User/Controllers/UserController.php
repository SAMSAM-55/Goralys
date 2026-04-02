<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\User\Controllers;

use Goralys\Core\User\Repository\Interfaces\UserRepositoryInterface;
use Goralys\Core\User\Repository\UserRepository;
use Goralys\Platform\DB\Interfaces\DbContainerInterface;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;

/**
 * The controller that handles the user logic.
 */
class UserController
{
    private LoggerInterface $logger;
    private DbContainerInterface $db;
    private UserRepositoryInterface $repo;

    /**
     * Initializes the logger and the database container used by the controller.
     * @param LoggerInterface $logger The injected logger.
     * @param DbContainerInterface $db The injected database container.
     */
    public function __construct(
        LoggerInterface $logger,
        DbContainerInterface $db,
    ) {
        $this->logger = $logger;
        $this->db = $db;

        $this->repo = new UserRepository($this->logger, $this->db);
    }

    /**
     * Deletes all users (except admins) from the database.
     * @return bool If the deletion was successful
     */
    public function clear(): bool
    {
        return $this->repo->clearAll();
    }
}
