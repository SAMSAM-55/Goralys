<?php

namespace Goralys\Core\Utils\User\Services;

use Goralys\Core\Utils\User\Interfaces\UsernameFormatterServiceInterface;

class UsernameFormatterService implements UsernameFormatterServiceInterface
{
    /**
     * @param string $username
     * @return string
     */
    public function formatUsername(string $username): string
    {
        // Expected format: first initial, dot, last name, optional digits.
        if (preg_match('/^([a-z])\.([a-z]+)\d*$/i', $username, $matches)) {
            $firstInitial = strtoupper($matches[1]);
            $lastName     = strtoupper($matches[2]);

            return $lastName . ' ' . $firstInitial . '.';
        }

        // Return the original value if it does not match the expected format.
        return $username;
    }
}
