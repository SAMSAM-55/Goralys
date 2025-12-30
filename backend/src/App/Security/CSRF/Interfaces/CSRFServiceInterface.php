<?php

namespace Goralys\App\Security\CSRF\Interfaces;

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;

interface CSRFServiceInterface
{
    public function getForForm(string $formId): string;
    public function create(string $formId): bool;
    public function validate(string $formId, RequestInterface $request): bool;
}
