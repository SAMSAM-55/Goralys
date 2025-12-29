<?php

namespace Goralys\Platform\Logger\Data\Enums;

enum LoggerInitiator: string
{
    case APP = "APP";
    case CORE = "CORE";
    case PLATFORM = "PLATFORM";
}
