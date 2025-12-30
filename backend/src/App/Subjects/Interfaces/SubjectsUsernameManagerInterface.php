<?php

namespace Goralys\App\Subjects\Interfaces;

interface SubjectsUsernameManagerInterface
{
    public function store(string $username): string;
    public function get(string $key): string;
}
