<?php

namespace Goralys\App\Config\Data;

enum RateLimitTimeMethod: string
{
    case CONSTANT = 'constant';
    case LINEAR = 'linear';
    case EXPONENTIAL = 'exponential';
}
