<?php

namespace Goralys\App\Config;

/**
 * Global application configuration constants.
 */
final class AppConfig
{
    public const int MAX_DRAFT_SIZE = 50 * 1024;

    public const int CSRF_TOKENS_SIZE = 8;
    public const int MAX_CSRF_TOKENS = 3;

    public const string BASE_STORAGE_DIR = __DIR__ . "/../../../";
}
