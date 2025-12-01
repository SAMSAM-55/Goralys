<?php

namespace Goralys\App\Security\CSRF\Interfaces;

interface CSRFServiceInterface
{
    public function getToken(): string;
    public function getForForm(string $formId): string;
    public function create(string $formId): bool;
    public function validate(string $formId, string $token): bool;
}
