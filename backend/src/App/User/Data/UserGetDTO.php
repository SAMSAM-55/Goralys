<?php

namespace Goralys\App\User\Data;

use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Data\UserFullDTO;
use JsonSerializable;

/**
 * The DTO used to send the users to the frontend
 */
final readonly class UserGetDTO implements JsonSerializable
{
    /**
     * @param string $fullName The full name of the user.
     * @param UserRole $role The role of the user.
     * @param string $publicId The public id of the user (uuid).
     */
    public function __construct(
        public string $fullName,
        public UserRole $role,
        public string $publicId,
    ) {}

    /**
     * Returns a {@see UserGetDTO}, which is used to transfer data to the frontend, from {@see UserFullDTO}.
     * @param UserFullDTO $user The user's complete information
     * @param string $publicId The public id of the user.
     * @return self The data to send to the frontend.
     */
    public static function fromFull(UserFullDTO $user, string $publicId): self
    {
        return new self(
            $user->fullName,
            $user->role,
            $publicId,
        );
    }

    /**
     * Transforms the DTO to a comprehensive JSON array that can then be sent to the frontend.
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'fullName' => $this->fullName,
            'role' => $this->role->toString(),
            'token' => $this->publicId,
        ];
    }
}
