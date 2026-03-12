<?php

namespace Goralys\Shared\Utils;

use Goralys\Shared\Utils\String\StringUtils;

class UtilitiesManager
{
    public StringUtils $string;

    public function __construct()
    {
        $this->string = new StringUtils();
    }
}
