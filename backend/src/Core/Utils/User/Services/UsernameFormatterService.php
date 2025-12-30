<?php

namespace Goralys\Core\Utils\User\Services;

use Goralys\Core\Utils\User\Interfaces\UsernameFormatterServiceInterface;

/**
 * The service used to format the username before sending it to the frontend.
 */
class UsernameFormatterService implements UsernameFormatterServiceInterface
{
    /**
     * Formats a username with default format f.lastnameX into LASTNAME F. with f the first letter of the first name and
     * X a random number between 0 and 9.
     * @param string $username The username
     * @return string The formated result.
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
