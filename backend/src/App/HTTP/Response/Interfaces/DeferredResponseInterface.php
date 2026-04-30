<?php

namespace Goralys\App\HTTP\Response\Interfaces;

use Goralys\App\Context\AppContext;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;

interface DeferredResponseInterface
{
    public function __construct(AppContext $context, int $responseCode = 200);

    public function toast(ToastType $type, string $title, string $message): self;
    public function error($message): self;
    public function redirect(string $path): self;
    public function action(string $action): self;
    public function send(): never;
}
