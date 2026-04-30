<?php

namespace Goralys\App\HTTP\Guard\Interface;

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\App\HTTP\Response\Interfaces\DeferredResponseInterface;

/**
 * Contract for guards that protect routes based on the current session user.
 */
interface GuardInterface
{
    /**
     * Checks that the value of the given request field matches the currently authenticated user.
     * @param RequestInterface $request The current HTTP request.
     * @param string $field The request field whose value should equal the session username.
     * @return DeferredResponseInterface|null A 403 deferred response if the check fails, null on success.
     */
    public function matchCurrentUser(RequestInterface $request, string $field): ?DeferredResponseInterface;
}
