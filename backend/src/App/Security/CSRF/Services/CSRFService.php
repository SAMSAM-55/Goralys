<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Security\CSRF\Services;

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Random\RandomException;

/**
 * Service to manage the CSRF tokens system.
 */
class CSRFService
{
    private LoggerInterface $logger;

    /**
     * Initializes the logger for the service.
     * @param LoggerInterface $logger The injected logger.
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Gets the token for a given form.
     * @param string $formId The id of the form.
     * @return string The retrieved token.
     */
    public function getForForm(string $formId): string
    {
        return $_SESSION["csrf-tokens-table"][$formId] ?? "";
    }

    /**
     * Creates a new CSRF token.
     * @param string $formId The id of the form to create the token for.
     * @return bool If the creation was successful or not.
     */
    public function create(string $formId): bool
    {
        try {
            $token = bin2hex(random_bytes(8));
            $_SESSION["csrf-tokens-table"][$formId] = $token;
            $this->logger->debug(
                LoggerInitiator::APP,
                "Successfuly created new token for form " . $formId . ", token : " . $token .
                ". New session : " . print_r($_SESSION, true)
            );
            return true;
        } catch (RandomException $e) {
            $this->logger->error(
                LoggerInitiator::APP,
                "An error occurred while generating a CSRF token for form : " . $formId . "\nError:" . $e->getMessage()
            );
            return false;
        }
    }

    /**
     * Validates a given CSRF token for a specific form.
     * It automatically invalidates the token even if the validation fails
     * @param string $formId The id of the form to verify the token for.
     * @param RequestInterface $request The current HTTP request
     * @return bool If the token is valid or not.
     */
    public function validate(string $formId, RequestInterface $request): bool
    {
        $token = $request->get('csrf-token');

        if (!isset($_SESSION["csrf-tokens-table"][$formId])) {
            $this->logger->error(
                LoggerInitiator::APP,
                "Foreign token form id encountered : " . $formId
            );
            $this->logger->debug(
                LoggerInitiator::APP,
                "Current session : " . print_r($_SESSION, true)
            );
            return false;
        }

        if ($_SESSION["csrf-tokens-table"][$formId] !== $token) {
            $this->logger->error(
                LoggerInitiator::APP,
                "Failed to validate token for form : " . $formId . "(" . $token . ")"
            );
            unset($_SESSION["csrf-tokens-table"][$formId]);
            return false;
        }

        unset($_SESSION["csrf-tokens-table"][$formId]);
        return true;
    }
}
