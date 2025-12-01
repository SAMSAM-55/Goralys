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

class AuthController implements AuthControllerInterface
{
    private GoralysLogger $logger;
    private DbContainer $db;

    public function __construct(
        GoralysLogger $logger,
        DbContainer $db
    ) {
        $this->logger = $logger;
        $this->db = $db;
    }

    /**
     * @param UserRegisterDTO $userData
     * @return bool
     * @throws GoralysQueryException|GoralysPrepareException
     */
    public function register(UserRegisterDTO $userData): bool
    {
        // Inputs sanitization
        $userData = new UserRegisterDTO(
            trim($userData->getUsername()),
            trim($userData->getFullName()),
            trim($userData->getPassword())
        );

        $validator = new RegisterValidatorService($this->db);
        $roleGetter = new GetUserRoleService($this->db);
        $userCreator = new CreateUserService($this->db);

        $service  = new RegisterService(
            $this->logger,
            $validator,
            $roleGetter,
            $userCreator
        );
        return $service->register($userData);
    }

    /**
     * @param UserLoginDTO $userData
     * @return bool
     * @throws GoralysQueryException|GoralysPrepareException
     */
    public function login(UserLoginDTO $userData): bool
    {
        $service = new LoginService($this->logger, $this->db);

        if (!$service->login($userData)) {
            return false;
        }

        $user = new UserRepository($this->logger, $this->db);
        $sessionData = $user->getByUsername($userData->getUsername());

        $_SESSION['current_id'] = $sessionData->getId();
        $_SESSION['current_full_name'] = $sessionData->getFullName();
        $_SESSION['current_username'] = $sessionData->getUsername();
        $_SESSION['current_role'] = $sessionData->getRole()->toString();

        return true;
    }

    /**
     * @return bool
     */
    public function logout(): bool
    {
        session_unset();
        session_destroy();
        return true;
    }
}
