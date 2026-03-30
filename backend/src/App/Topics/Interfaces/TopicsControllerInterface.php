<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\Topics\Interfaces;

use Goralys\App\HTTP\Files\Data\UploadedFileDTO;
use Goralys\Core\Topics\Data\TopicDTO;

interface TopicsControllerInterface
{
    /* @return TopicDTO[] */
    public function makeTopicsFromZip(UploadedFileDTO $file): array;
    public function makeTopic(string $name, string $code, array $students, array $teachers): TopicDTO;

    public function insert(TopicDTO $topic): void;
    public function exportUsernames(array $topics): string;
    public function clear(): bool;
}
