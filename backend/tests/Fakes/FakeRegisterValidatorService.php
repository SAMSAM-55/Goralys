<?php

namespace Goralys\Tests\Fakes;

use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\Core\User\Interfaces\RegisterValidatorServiceInterface;

class FakeRegisterValidatorService implements RegisterValidatorServiceInterface
{
    private bool $canRegister = true;

    public function canRegister(UserRegisterDTO $data): bool
    {
        return $this->canRegister;
    }

    public function setCanRegister(bool $canRegister): void
    {
        $this->canRegister = $canRegister;
    }
}
