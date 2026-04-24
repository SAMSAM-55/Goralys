<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Repository;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Data\UserCreateDTO;
use Goralys\Core\User\Data\UserFullDTO;
use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Core\User\Repository\Interfaces\UserRepositoryInterface;
use Goralys\Platform\DB\Interfaces\DbContainerInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\User\UserNotFoundException;
use mysqli_result;

/**
 * The repository used to get user's info from the database.
 */
class UserRepository implements UserRepositoryInterface
{
    private LoggerInterface $logger;
    private DbContainerInterface $db;

    /**
     * Initializes the logger and the database container for the repository.
     * @param LoggerInterface $logger The injected logger.
     * @param DbContainerInterface $db The injected database container.
     */
    public function __construct(
        LoggerInterface $logger,
        DbContainerInterface $db
    ) {
        $this->logger = $logger;
        $this->db = $db;
    }

    /**
     * This helper is used to build a user DTO containing all the user's info (full) from a database request's result,
     * it is in charge of the logging process.
     * @param mysqli_result $result The result from the database.
     * @return UserFullDTO All the user's info.
     * @throws UserNotFoundException If the user is invalid.
     */
    private function buildUserFromResult(mysqli_result $result): UserFullDTO
    {
        if ($result->num_rows === 0) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "Failed to fetch the user's data from the database."
            );
            throw new UserNotFoundException("Invalid user provided.");
        }

        $row = $result->fetch_assoc();

        $this->logger->info(
            LoggerInitiator::CORE,
            "User's data were successfully fetched for user : " . $row['user_id']
        );
        return new UserFullDTO(
            (int) $row['id'],
            $row['user_id'],
            UserRole::fromString($row['role']),
            $row['full_name']
        );
    }

    /**
     * Get a user's info with its username.
     * @param string $username The user's username.
     * @return UserFullDTO The user's info.
     * @throws UserNotFoundException If the user is invalid.
     */
    public function getByUsername(string $username): UserFullDTO
    {
        $result = $this->db->fetch(
            "select id, user_id, role, full_name from users where user_id = ?",
            "s",
            $username
        );

        return $this->buildUserFromResult($result);
    }

    /**
     * Saves a new user to the database
     * @param UserCreateDTO $userData The necessary data to save the user
     * @return bool If the save was successful or not
     */
    public function save(UserCreateDTO $userData): bool
    {
        return $this->db->run(
            "insert into users (user_id, full_name, password_hash, role, public_id) values (?, ?, ?, ?, uuid())",
            "ssss",
            $userData->username,
            $userData->fullName,
            $userData->passwordHash,
            $userData->role->toString()
        );
    }

    /**
     * Checks if a user exists inside the database.
     * @param string $username The user's username.
     * @return bool If the user exits or not.
     */
    public function exists(string $username): bool
    {
        return $this->db->fetch(
            "select 1 from users where user_id = ? limit 1",
            "s",
            $username
        )->num_rows != 0;
    }

    /**
     * Checks if a username is valid.
     * @param string $username The user's username.
     * @return bool If the username is valid or not.
     */
    public function isUsernameValid(string $username): bool
    {
        return $this->db->fetch(
            "select 1
            where exists(select 1 from student_topics where student_id = ?)
            or exists(select 1 from topic_teachers where teacher_id = ?)
            or exists(select 1 from admins_list where user_id = ?)
            limit 1",
            "sss",
            $username,
            $username,
            $username
        )->num_rows != 0;
    }

    /**
     * Checks if a user exits with a given username.
     * If it exists, it returns a new login DTO.
     * @param string $username
     * @return UserLoginDTO|null
     */
    public function getLoginDTO(string $username): ?UserLoginDTO
    {
        $result = $this->db->fetch(
            "select password_hash from users where user_id = ?",
            "s",
            $username
        );

        if ($result->num_rows === 0) {
            $this->logger->warning(
                LoggerInitiator::CORE,
                "Failed to connect user, invalid username : " . $username
            );
            return null;
        }

        $row = $result->fetch_assoc();

        return new UserLoginDTO($username, $row['password_hash']);
    }

    /**
     * Gets the role of a user.
     * @param string $username The user's username.
     * @return ?UserRole The user's role.
     */
    public function getRoleForUsername(string $username): ?UserRole
    {
        $result = $this->db->fetch(
            "select user_id, role from (
            select student_id as user_id, 'student' as role
            from student_topics
            union all
            select teacher_id as user_id, 'teacher' as role
            from topic_teachers
            union all
            select user_id as user_id, 'admin' as role
            from admins_list
            ) as all_ids
            where user_id = ?
            limit 1",
            "s",
            $username
        );

        if ($result->num_rows === 0) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "No such user : " . $username
            );
            return null;
        }

        $role = $result->fetch_assoc()['role'];
        return UserRole::fromString($role);
    }

    /**
     * Gets the full name of a user.
     * @param string $username The user's username.
     * @return ?string The user's full name.
     */
    public function getFullNameForUsername(string $username): ?string
    {
        $result = $this->db->fetch(
            "select full_name from users where user_id = ?
            limit 1",
            "s",
            $username
        );

        if ($result->num_rows === 0) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "No such user : " . $username
            );
            return null;
        }

        return $result->fetch_assoc()['full_name'];
    }

    /**
     * Deletes all users (except admins) from the database.
     * @return bool If the deletion was successful
     */
    public function clearAll(): bool
    {
        return $this->db->runNoArgs("
            delete from users
            where role <> 'admin'
        ");
    }

    /**
     * @param string $uuid
     * @return UserFullDTO
     * @throws UserNotFoundException
     */
    public function getByPublicId(string $uuid): UserFullDTO
    {
        $result = $this->db->fetch(
            "select id, user_id, role, full_name from users where public_id = ?",
            "s",
            $uuid
        );

        return $this->buildUserFromResult($result);
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function isPublicIdValid(string $uuid): bool
    {
        return $this->db->fetch("select 0 from users where public_id = ?", "s", $uuid)->num_rows !== 0;
    }

    /**
     * @param string $username
     * @return string|null
     */
    public function getPublicIdForUsername(string $username): ?string
    {
        return $this->db->fetch(
            "select public_id from users where user_id = ?",
            "s",
            $username
        )->fetch_assoc()['public_id'] ?? null;
    }
}
