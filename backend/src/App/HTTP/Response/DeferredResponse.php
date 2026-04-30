<?php

namespace Goralys\App\HTTP\Response;

use Goralys\App\Context\AppContext;
use Goralys\App\Context\Data\ToastMode;
use Goralys\App\HTTP\Response\Interfaces\DeferredResponseInterface;
use Goralys\App\Utils\Toast\Controllers\ToastController;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\App\Utils\Toast\Data\ToastDTO;
use Goralys\Shared\Exception\GoralysRuntimeException;
use JetBrains\PhpStorm\NoReturn;

class DeferredResponse implements Interfaces\DeferredResponseInterface
{
    private AppContext $context;
    private ?ToastDTO $toast = null;
    private ?string $action = null;
    private ToastController $controller;
    private int $code;

    public function __construct(AppContext $context, int $responseCode = 200)
    {
        $this->context = $context;
        $this->code = $responseCode;
        $this->controller = new ToastController();
    }

    public function toast(ToastType $type, string $title, string $message): self
    {
        $this->toast = $this->controller->builder->buildToast(
            $type,
            $title,
            $message,
            "/",
            $this->context->mode === ToastMode::FLASH
        );
        return $this;
    }

    public function error($message): self
    {
        $this->toast = $this->controller->builder->buildToast(
            ToastType::ERROR,
            "Erreur",
            $message,
            "/",
            $this->context->mode === ToastMode::FLASH
        );
        return $this;
    }

    public function redirect(string $path): self
    {
        $destination = $this->context->originDomain . trim($path, "/") . "/";
        $this->toast->redirect = $destination;
        return $this;
    }

    public function action(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @throws GoralysRuntimeException
     */
    #[NoReturn]
    public function send(): never
    {
        if (!$this->toast) {
            throw new GoralysRuntimeException("Can not send null toast");
        }
        if ($this->context->mode === ToastMode::FLASH) {
            http_response_code(302);
            $this->controller->responder->sendToast($this->toast, $this->action ?? "");
            header('Location: '  . $this->toast->redirect);
            exit;
        }
        http_response_code($this->code);
        $this->controller->responder->sendToast($this->toast, $this->action ?? "");
        exit;
    }
}
