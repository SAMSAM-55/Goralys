<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\User\Repository\Interfaces;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Data\UserCreateDTO;
use Goralys\Core\User\Data\UserFullDTO;
use Goralys\Core\User\Data\UserLoginDTO;

/**
 * Contract for the user repository.
 * Covers all read/write operations on user records in the database.
 */
interface UserRepositoryInterface
{
    /**
     * @param string $username The user's username.
     * @return UserFullDTO The full user record.
     */
    public function getByUsername(string $username): UserFullDTO;

    /**
     * @param string $uuid The user's public UUID.
     * @return UserFullDTO The full user record.
     */
    public function getByPublicId(string $uuid): UserFullDTO;

    /**
     * @param string $username The username to check.
     * @return bool Whether a user with this username exists.
     */
    public function exists(string $username): bool;

    /**
     * Checks whether a username is present in the pre-approved list and therefore allowed to register.
     * @param string $username The username to check.
     * @return bool Whether the username is valid for registration.
     */
    public function isUsernameValid(string $username): bool;

    /**
     * @param string $uuid The public UUID to check.
     * @return bool Whether the UUID corresponds to a known user.
     */
    public function isPublicIdValid(string $uuid): bool;

    /**
     * @param UserCreateDTO $userData The data required to persist the new user.
     * @return bool If the save was successful or not.
     */
    public function save(UserCreateDTO $userData): bool;

    /**
     * @param string $username The user's username.
     * @return UserLoginDTO|null The login credentials, or null if the user does not exist.
     */
    public function getLoginDTO(string $username): ?UserLoginDTO;

    /**
     * @param string $username The user's username.
     * @return UserRole|null The user's role, or null if the user does not exist.
     */
    public function getRoleForUsername(string $username): ?UserRole;

    /**
     * @param string $username The user's username.
     * @return string|null The user's full name, or null if the user does not exist.
     */
    public function getFullNameForUsername(string $username): ?string;

    /**
     * @param string $username The user's username.
     * @return string|null The user's public UUID, or null if the user does not exist.
     */
    public function getPublicIdForUsername(string $username): ?string;

    /**
     * Returns all the users inside the database.
     * @return UserFullDTO[] The users.
     */
    public function getAll(): array;

    /**
     * Deletes a user only from the `users` table. This is used to reset the user's password.
     * @param string $username The user's username.
     * @return bool Wether the deletion was successful.
     */
    public function softDelete(string $username): bool;

    /**
     * Deletes a user from all the database's tables. This is used to completely remove a user and its associated
     * subjects and topics (teachers only).
     * @param string $username The user's username.
     * @return bool Wether the deletion was successful.
     */
    public function hardDelete(string $username): bool;

    /**
     * Removes all non-admin users from the database.
     * @return bool If the deletion was successful or not.
     */
    public function clearAll(): bool;
}
