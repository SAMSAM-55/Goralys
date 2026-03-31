<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\Logger\Interfaces;

use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;

interface LoggerInterface
{
    public function rotate(): void;

    public function info(
        LoggerInitiator $initiator,
        string $message,
    ): void;

    public function debug(
        LoggerInitiator $initiator,
        string $message,
    ): void;

    public function warning(
        LoggerInitiator $initiator,
        string $message,
    ): void;

    public function error(
        LoggerInitiator $initiator,
        string $message,
    ): void;

    public function fatal(
        LoggerInitiator $initiator,
        string $message,
    ): void;
}
