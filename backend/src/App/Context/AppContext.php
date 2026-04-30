<?php

namespace Goralys\App\Context;

use Goralys\App\Context\Data\ToastMode;

class AppContext
{
    public function __construct(
        public ToastMode $mode,
        public string $originDomain
    ) {
    }
}
