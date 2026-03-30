<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\User\Controllers;

use Goralys\App\User\Data\Enums\UserAuthStatus;
use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\App\User\Interfaces\AuthControllerInterface;
use Goralys\Core\User\Repository\UserRepository;
use Goralys\Core\User\Services\CreateUserService;
use Goralys\Core\User\Services\GetUserRoleService;
use Goralys\Core\User\Services\LoginService;
use Goralys\Core\User\Services\RegisterService;
use Goralys\Core\User\Services\RegisterValidatorService;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use Goralys\Shared\Exception\User\UserNotFoundException;

/**
 * The controller that handles the authentification logic (register, login and logout).
 */
class AuthController implements AuthControllerInterface
{
    private LoggerInterface $logger;
    private DbContainer $db;
    private UserRepository $repo;
    /**
     * The lifetime of the PHP session, this variable is passed by the kernel when the controller is constructed.
     * @var int
     */
    private readonly int $sessionLifetime;
    private readonly float $sessionMultiplier;

    /**
     * Initializes the logger and the database container used by the controller.
     * @param LoggerInterface $logger The injected logger.
     * @param DbContainer $db The injected database container.
     * @param int $sessionLifetime The lifetime of the PHP session.
     * @param float $sessionLifetimeMultiplier The lifetime multiplier of the PHP session.
     */
    public function __construct(
        LoggerInterface $logger,
        DbContainer $db,
        int $sessionLifetime,
        float $sessionLifetimeMultiplier
    ) {
        $this->logger = $logger;
        $this->db = $db;
        $this->sessionLifetime = $sessionLifetime;
        $this->sessionMultiplier = $sessionLifetimeMultiplier;

        $this->repo = new UserRepository($this->logger, $this->db);
    }

    /**
     * Registers a new user via a register service.
     * @param UserRegisterDTO $userData The necessary data to register the user.
     * @return bool If the creation was successful or not.
     * @throws GoralysQueryException|GoralysPrepareException Only thrown if the database request goes wrong.
     * @throws UserNotFoundException If the user id was invalid.
     */
    public function register(UserRegisterDTO $userData): bool
    {
        $userData = new UserRegisterDTO(
            $userData->getUsername(),
            $userData->getFullName(),
            $userData->getPassword()
        );

        $validator = new RegisterValidatorService($this->repo);
        $roleGetter = new GetUserRoleService($this->repo);
        $userCreator = new CreateUserService($this->repo);

        $service  = new RegisterService(
            $this->logger,
            $validator,
            $roleGetter,
            $userCreator
        );
        return $service->register($userData);
    }

    /**
     * Login the user via a login service.
     * @param UserLoginDTO $userData The necessary credentials to log in the user.
     * @return bool If the login was successful or not.
     * @throws GoralysQueryException|GoralysPrepareException Only thrown if the database request goes wrong.
     */
    public function login(UserLoginDTO $userData): bool
    {
        $service = new LoginService($this->logger, $this->repo);

        try {
            if (!$service->login($userData)) {
                return false;
            }

            session_regenerate_id(true);
            $sessionData = $this->repo->getByUsername($userData->getUsername());

            $_SESSION['current_id'] = $sessionData->getId();
            $_SESSION['current_full_name'] = $sessionData->getFullName();
            $_SESSION['current_username'] = $sessionData->getUsername();
            $_SESSION['current_role'] = $sessionData->getRole()->toString();

            return true;
        } catch (UserNotFoundException) {
            return false;
        }
    }

    /**
     * Logs the user out.
     * @return bool If the logout was successful or not.
     */
    public function logout(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false; // already logged out, do nothing
        }

        session_unset();
        session_destroy();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        return true;
    }

    /**
     * Checks if the user is authenticated.
     * The authentification cookie expires after an hour.
     * @param int $sinceLastConnection The time elapsed since the last user connection
     * @return UserAuthStatus If the user is authenticated.
     */
    public function getAuthStatus(int $sinceLastConnection): UserAuthStatus
    {
        if (!isset($_SESSION) || !isset($_SESSION['current_id'])) {
            return UserAuthStatus::NOT_AUTHENTICATED;
        } elseif (
            $sinceLastConnection > $this->sessionMultiplier * $this->sessionLifetime
            || $sinceLastConnection === -1
        ) {
            return UserAuthStatus::NOT_AUTHENTICATED;
        }

        return $sinceLastConnection > $this->sessionLifetime
                ? UserAuthStatus::SESSION_EXPIRED
                : UserAuthStatus::AUTHENTICATED;
    }
}
