<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Utils\Toast\Services;

// Toast specific classes
use Goralys\App\Utils\Toast\Data\ToastDTO;

/**
 * The service used to send a toast to the frontend
 */
class ToastResponderService
{
    private ToastFlashService $flashService;

    public function __construct(ToastFlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    /**
     * Sends a toast to the frontend.
     * If the `isJS` property is set to `true`, the toast will be sent as a JSON object and then parsed by the frontend.
     * Else, the toast will be sent via url params that will then be read by the frontend to display the toast.
     * @param ToastDTO $toastData The data of the toast.
     * @param string $action The action to perform when the toast is sent to the frontend.
     * @return void
     */
    public function sendToast(ToastDTO $toastData, string $action = ""): void
    {
        if ($toastData->flash) {
            $this->flashService->store($toastData, $action);
            return;
        }

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($toastData->toastInfo, JSON_UNESCAPED_UNICODE);
    }
}
