<?php

namespace Goralys\Core\Utils\User\Interfaces;

interface UsernameFormatterServiceInterface
{
    public function formatUsername(string $username): string;
}
