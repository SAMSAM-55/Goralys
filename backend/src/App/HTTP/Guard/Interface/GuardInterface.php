<?php

namespace Goralys\App\HTTP\Guard\Interface;

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\App\HTTP\Response\Interfaces\DeferredResponseInterface;

interface GuardInterface
{
    public function matchCurrentUser(RequestInterface $request, string $field): ?DeferredResponseInterface;
}
