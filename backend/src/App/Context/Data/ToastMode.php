<?php

namespace Goralys\App\Context\Data;

/**
 * Defines how toasts are delivered to the frontend:
 * FLASH stores the toast in the session to be shown on the next page load (flash toast retrieval endpoint call),
 * DEFAULT sends it inline as a JSON response.
 */
enum ToastMode
{
    case FLASH;
    case DEFAULT;
}
