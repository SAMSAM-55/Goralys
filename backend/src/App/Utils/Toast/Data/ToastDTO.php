<?php

namespace Goralys\App\Utils\Toast\Data;

/**
 * The DTO used to transport the data of a toast
 */
class ToastDTO
{
    private array $toastInfo;
    private string $redirect;
    private bool $JS;

    public function __construct(
        array $toastInfo,
        string $redirect,
        bool $isJS
    ) {
        $this->toastInfo = $toastInfo;
        $this->redirect = $redirect;
        $this->JS = $isJS;
    }

    // Getters
    final public function getToastInfo(): array
    {
        return $this->toastInfo;
    }
    final public function getRedirect(): string
    {
        return $this->redirect;
    }
    final public function isJs(): bool
    {
        return $this->JS;
    }
}
