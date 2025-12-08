<?php

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\UserCreateDTO;
use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\Core\User\Interfaces\RegisterServiceInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use Goralys\Shared\Exception\User\UserNotFoundException;

class RegisterService implements RegisterServiceInterface
{
    private GoralysLogger $logger;
    private RegisterValidatorService $validator;
    private GetUserRoleService $roleGetter;
    private CreateUserService $userCreator;

    /**
     * Initializes the logger and all the service's sub-services.
     * @param GoralysLogger $logger The injected logger
     * @param RegisterValidatorService $validator The injected validator.
     * It is used to verify that the can register
     * @param GetUserRoleService $roleGetter The injected role getter.
     * It is used to retrieve the user's role and assign iot automatically.
     * @param CreateUserService $userCreator The injected user creator.
     * It is used to create the user inside the database.
     */
    public function __construct(
        GoralysLogger $logger,
        RegisterValidatorService $validator,
        GetUserRoleService $roleGetter,
        CreateUserService $userCreator
    ) {
        $this->logger = $logger;

        $this->validator = $validator;
        $this->roleGetter = $roleGetter;
        $this->userCreator = $userCreator;
    }

    /**
     * Register a new user to the database.
     * @param UserRegisterDTO $data The necessary data to register the user.
     * @return bool If the register process was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     * @throws UserNotFoundException If the user is not found.
     */
    public function register(UserRegisterDTO $data): bool
    {
        if (!$this->validator->canRegister($data)) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "Failed to register user with user name : " . $data->getUsername()
            );
            return false;
        }

        $createData = new UserCreateDTO(
            $data->getUsername(),
            $data->getFullName(),
            password_hash($data->getPassword(), PASSWORD_DEFAULT),
            $this->roleGetter->getRoleByUsername($data->getUsername())
        );

        if (!$this->userCreator->createUser($createData)) {
            $this->logger->error(
                LoggerInitiator::CORE,
                "Failed to create user with user name : " . $data->getUsername()
            );
            return false;
        }
        $this->logger->info(
            LoggerInitiator::CORE,
            "Successfully registered a new user with username : "
            . $data->getUsername() . "(" . $createData->getRole()->toString() . ")"
        );
        return true;
    }
}
