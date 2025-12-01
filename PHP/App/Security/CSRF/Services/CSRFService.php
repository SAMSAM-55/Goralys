<?php

namespace Goralys\App\Security\CSRF\Services;

use Goralys\App\Security\CSRF\Interfaces\CSRFServiceInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\GoralysLogger;
use Random\RandomException;

class CSRFService implements CSRFServiceInterface
{
    private GoralysLogger $logger;

    public function __construct(
        GoralysLogger $logger
    ) {
        $this->logger = $logger;
    }

    public function getToken(): string
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['csrf-token'])) {
                return $_POST['csrf-token'];
            }
            return "";
        }

        $rawInput = file_get_contents("php://input");
        if (!$rawInput) {
            return "";
        }

        $decoded = json_decode($rawInput, true);

        if (!is_array($decoded)) {
            return "";
        }

        return $decoded['csrf-token'] ?? "";
    }

    /**
     * @param string $formId
     * @return string
     */
    public function getForForm(string $formId): string
    {
        return $_SESSION[$formId . "-csrf-token"] ?? "";
    }

    /**
     * @param string $formId
     * @return bool
     */
    public function create(string $formId): bool
    {
        try {
            $token = bin2hex(random_bytes(8));
            $_SESSION[$formId . "-csrf-token"] = $token;
            return true;
        } catch (RandomException $e) {
            $this->logger->error(
                LoggerInitiator::APP,
                "An occured while generating a CSRF token for form : " . $formId
            );
            return false;
        }
    }

    /**
     * Validates a given CSRF token for a specific form
     * @param string $formId The id of the form to verify the token for
     * @param string $token The CSRF token
     * @return bool
     */
    public function validate(string $formId, string $token): bool
    {
        if (!isset($_SESSION[$formId . "-csrf-token"])) {
            $this->logger->error(
                LoggerInitiator::APP,
                "Foreign token form id encountered : " . $formId
            );
            return false;
        }

        if ($_SESSION[$formId . "-csrf-token"] !== $token) {
            $this->logger->error(
                LoggerInitiator::APP,
                "Failed to validate token for form : " . $formId . "(" . $token . ")"
                . "\nCurrent tokens : " . print_r($_SESSION, true)
            );
            unset($_SESSION[$formId . "-csrf-token"]);
            return false;
        }

        unset($_SESSION[$formId . "-csrf-token"]);
        return true;
    }
}
