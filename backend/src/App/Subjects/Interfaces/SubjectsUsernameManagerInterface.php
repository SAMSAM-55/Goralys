<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Subjects\Interfaces;

interface SubjectsUsernameManagerInterface
{
    public function store(string $username): string;
    public function get(string $key): string;
}
