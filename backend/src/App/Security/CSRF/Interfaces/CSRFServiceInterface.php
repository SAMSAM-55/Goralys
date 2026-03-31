<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Security\CSRF\Interfaces;

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;

interface CSRFServiceInterface
{
    public function getForForm(string $formId): string;
    public function create(string $formId): bool;
    public function validate(string $formId, RequestInterface $request): bool;
}
