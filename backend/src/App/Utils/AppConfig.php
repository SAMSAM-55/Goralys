<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Utils;

/**
 * Small secondary environment loader to load the `FOLDER` variable necessary to correctly send the toasts.
 * Just like the rest of the environment, this variable is defined inside the `.env` file in the project root.
 */
class AppConfig
{
    private static string $folder;

    /**
     * Gets the `FOLDER` variable from the environment.
     */
    public function __construct()
    {
        self::$folder = $_ENV["FOLDER"];
    }

    /**
     * Returns the `FOLDER` environment variable.
     * This variable is used to generate the urls to send the toasts.
     * It corresponds to the subfolder that your site may be in when running on a local server.
     * It is recommended to set it to "/" for production.
     * @return string The `FOLDER` variable.
     */
    final public function getFolder(): string
    {
        return self::$folder;
    }
}
