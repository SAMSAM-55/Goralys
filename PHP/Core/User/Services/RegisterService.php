<?php

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\UserCreateDTO;
use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\Core\User\Interfaces\RegisterServiceInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

class RegisterService implements RegisterServiceInterface
{
    private GoralysLogger $logger;
    private RegisterValidatorService $validator;
    private GetUserRoleService $roleGetter;
    private CreateUserService $userCreator;

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
     * @param UserRegisterDTO $data
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
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
