<?php

namespace Goralys\App\User\Data;

use JsonSerializable;

class UserCollection implements JsonSerializable
{
    private array $users = [] {
        get {
            return $this->users;
        }
    }

    /**
     * Adds a new user to the collection.
     * @param UserGetDTO $newUser The user to add.
     * @return void
     */
    public function addUser(UserGetDTO $newUser): void
    {
        $this->users = [...$this->users, $newUser];
    }

    /**
     * Transforms the raw php array into a JSON array that can then be sent to the frontend.
     * @return UserGetDTO[]
     */
    public function jsonSerialize(): array
    {
        return $this->users;
    }
}
