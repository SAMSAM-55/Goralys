<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\User\Controllers;

use Goralys\App\User\Data\UserCollection;
use Goralys\App\User\Data\UserGetDTO;
use Goralys\App\User\Data\UsernameTable;
use Goralys\App\User\Services\UsernameManager;
use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Core\User\Repository\Interfaces\UserRepositoryInterface;
use Goralys\Core\User\Repository\UserRepository;
use Goralys\Core\User\Services\LoginService;
use Goralys\Platform\DB\Interfaces\DbContainerInterface;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\GoralysRuntimeException;
use Goralys\Shared\Exception\User\UserNotFoundException;
use Goralys\Shared\Utils\UtilitiesManager;

/**
 * The controller that handles the user logic.
 */
final class UserController
{
    private LoggerInterface $logger;
    private DbContainerInterface $db;
    private UserRepositoryInterface $repo;
    private UsernameManager $usernames;

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
        $this->usernames = new UsernameManager($this->repo);
    }

    /**
     * Deletes all users (except admins) from the database.
     * @return bool If the deletion was successful
     */
    public function clear(): bool
    {
        return $this->repo->clearAll();
    }

    /**
     * Returns all non-admin users from the database.
     * @return UserCollection The users (teachers and students).
     */
    public function getAll(): UserCollection
    {
        $result = new UserCollection();
        foreach ($this->repo->getAll() as $user) {
            $result->addUser(
                UserGetDTO::fromFull(
                    $user,
                    $this->repo->getPublicIdForUsername($user->username),
                ),
            );
        }
        return $result;
    }

    /**
     * Checks if a password is correct for the current user.
     * @param string $password The password to check.
     * @return bool Wether the password is correct.
     * @throws UserNotFoundException If the user does not exists.
     */
    public function validatePassword(string $password): bool
    {
        $service = new LoginService($this->logger, $this->repo);
        return $service->checkPassword(new UserLoginDTO($_SESSION['current_username'], $password));
    }

    /**
     * Deletes a user partially (consult {@see UserRepositoryInterface::softDelete()} for more information) to allow it
     * to recreate his account and thus choose a new password.
     * @param string $publicId The user's public id.
     * @return bool Wether the operation was successful.
     * @throws GoralysRuntimeException If the username of the user could not be retrieved.
     */
    public function resetPassword(string $publicId): bool
    {
        return $this->repo->softDelete($this->usernames->get($publicId));
    }

    /**
     * Replaces a teacher inside the database.
     * @param string $publicId The current teacher's public id.
     * @param string $newName The full name of the new teacher.
     * @return string|null Wether the operation was successful.
     * @throws GoralysRuntimeException If the username of the user could not be retrieved.
     */
    public function replaceTeacher(string $publicId, string $newName): ?string
    {
        $utils = new UtilitiesManager();
        $table = new UsernameTable($utils);
        $old = $this->usernames->get($publicId);
        $new = $table->resolve($newName);
        return ($this->repo->softDelete($old) && $this->repo->replaceTeacher($old, $new)) ? $new : null;
    }

    /**
     * Deletes a user completely (consult {@see UserRepositoryInterface::hardDelete()} for more information).
     * @param string $publicId The user's public id.
     * @return bool Wether the operation was successful.
     * @throws GoralysRuntimeException If the username of the user could not be retrieved.
     */
    public function delete(string $publicId): bool
    {
        return $this->repo->hardDelete($this->usernames->get($publicId));
    }
}
