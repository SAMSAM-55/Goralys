<?php

namespace Goralys\App\HTTP\Guard;

use Goralys\App\Context\AppContext;
use Goralys\App\HTTP\Guard\Interface\GuardInterface;
use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\App\HTTP\Response\DeferredResponse;
use Goralys\App\HTTP\Response\Interfaces\DeferredResponseInterface;
use Goralys\App\Subjects\Services\UsernameManager;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;

class HttpGuard implements GuardInterface
{
    private AppContext $context;
    private UsernameManager $usernameManager;

    public function __construct(UsernameManager $usernameManager, AppContext $context)
    {
        $this->usernameManager = $usernameManager;
        $this->context = $context;
    }

    public function matchCurrentUser(RequestInterface $request, string $field): ?DeferredResponseInterface
    {
        $actual = $_SESSION['current_username'] ?? null;
        $given = $request->get($field);
        if ($actual && ($given == $actual || $this->usernameManager->get($given) == $actual)) {
            return null;
        }

        return new DeferredResponse($this->context, 403)->toast( // Unauthorized
            ToastType::WARNING,
            "Mauvais utilisateur",
            "Il semblerait que vous ne soyez pas le bon utilisateur"
        );
    }
}
