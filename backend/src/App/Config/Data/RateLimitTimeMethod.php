<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Config\Data;

/**
 * Defines the strategy used to compute the penalty window when a rate limit is exceeded.
 */
enum RateLimitTimeMethod: string
{
    case CONSTANT = 'constant';
    case LINEAR = 'linear';
    case EXPONENTIAL = 'exponential';
}
