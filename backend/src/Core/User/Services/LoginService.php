<?php

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Core\User\Interfaces\LoginServiceInterface;
use Goralys\Core\User\Repository\Interfaces\UserRepositoryInterface;
use Goralys\Core\User\Repository\UserRepository;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use Goralys\Shared\Exception\User\UserNotFoundException;

class LoginService implements LoginServiceInterface
{
    private LoggerInterface $logger;
    private UserRepositoryInterface $repo;

    /**
     * Initializes the logger and the user repository used by the service.
     * @param LoggerInterface $logger The injected logger.
     * @param UserRepositoryInterface $repo The injected user repository.
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepositoryInterface $repo
    ) {
        $this->logger = $logger;
        $this->repo = $repo;
    }

    /**
     * Logs in a user by using its password and username.
     * @param UserLoginDTO $userData The necessary credentials to log the user in.
     * @return bool If the login was successful or not.
     * @throws GoralysQueryException|GoralysPrepareException Only thrown if the request goes wrong.
     * @throws UserNotFoundException If the user is not found.
     */
    public function login(UserLoginDTO $userData): bool
    {
        $login = $this->repo->getLoginDTO($userData->getUsername());

        if ($login === null) {
            throw new UserNotFoundException("No such user : " . $userData->getUsername());
        }

        $passwordHash = $login->getPassword();

        if (!password_verify($userData->getPassword(), $passwordHash)) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "Failed to connect user, invalid password " . " for user : " . $userData->getUsername()
            );
            return false;
        }

        $this->logger->info(
            LoggerInitiator::CORE,
            "New user logged in : " . $userData->getUsername()
        );
        return true;
    }
}
