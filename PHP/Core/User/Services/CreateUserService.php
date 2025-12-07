<?php

namespace Goralys\Core\User\Services;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Data\UserCreateDTO;
use Goralys\Core\User\Interfaces\CreateUserInterface;
use Goralys\Core\User\Repository\UserRepository;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

/**
 * The service used to create users.
 */
class CreateUserService implements CreateUserInterface
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
     * Creates a new user inside the database.
     * @param UserCreateDTO $userData The necessary data to create the user inside the database.
     * @return bool If the creation was successful or not.
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function createUser(UserCreateDTO $userData): bool
    {
        if ($userData->getRole() == UserRole::UNKNOWN) {
            return false;
        }
        return $this->repo->save($userData);
    }
}
