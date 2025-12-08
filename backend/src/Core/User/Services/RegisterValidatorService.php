<?php

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\Core\User\Interfaces\RegisterValidatorServiceInterface;
use Goralys\Core\User\Repository\UserRepository;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

/**
 * Service used to determine if a user can register or not.
 */
class RegisterValidatorService implements RegisterValidatorServiceInterface
{
    private UserRepository $repo;

    /**
     * Initializes the user repository used by the service.
     * @param UserRepository $repo The injected user repository.
     */
    public function __construct(
        UserRepository $repo
    ) {
        $this->repo = $repo;
    }


    /**
     * Checks if a user can register.
     * @param UserRegisterDTO $data The user's data.
     * @return bool If the user can register or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function canRegister(UserRegisterDTO $data): bool
    {
        $exits = $this->repo->exits($data->getUsername());
        $valid = $this->repo->isUsernameValid($data->getUsername());

        return $valid && !$exits;
    }
}
