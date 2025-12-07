<?php

namespace Goralys\App\User\Controllers;

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
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use Goralys\Shared\Exception\User\UserNotFoundException;

/**
 * The controller that handles the authentification logic (register, login and logout).
 */
class AuthController implements AuthControllerInterface
{
    private GoralysLogger $logger;
    private DbContainer $db;
    private UserRepository $repo;

    /**
     * Initializes the logger and the database container used by the controller.
     * @param GoralysLogger $logger
     * @param DbContainer $db
     */
    public function __construct(
        GoralysLogger $logger,
        DbContainer $db
    ) {
        $this->logger = $logger;
        $this->db = $db;

        $this->repo = new UserRepository($this->logger, $this->db);
    }

    /**
     * Registers a new user via a register service.
     * @param UserRegisterDTO $userData The necessary data to register the user.
     * @return bool If the creation was successful or not.
     * @throws GoralysQueryException|GoralysPrepareException Only thrown if the database request goes wrong.
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
     * @throws UserNotFoundException If the user could not be found.
     */
    public function login(UserLoginDTO $userData): bool
    {
        $service = new LoginService($this->logger, $this->repo);

        if (!$service->login($userData)) {
            return false;
        }

        $sessionData = $this->repo->getByUsername($userData->getUsername());

        $_SESSION['current_id'] = $sessionData->getId();
        $_SESSION['current_full_name'] = $sessionData->getFullName();
        $_SESSION['current_username'] = $sessionData->getUsername();
        $_SESSION['current_role'] = $sessionData->getRole()->toString();

        return true;
    }

    /**
     * Log the user out.
     * @return bool If the logout was successful or not.
     */
    public function logout(): bool
    {
        session_unset();
        session_destroy();
        return true;
    }
}
