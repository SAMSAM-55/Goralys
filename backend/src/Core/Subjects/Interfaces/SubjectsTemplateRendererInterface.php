<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Subjects\Interfaces;

use Goralys\Core\Subjects\Data\StudentSubjectsDTO;
use Goralys\Platform\Doc\PDF\Data\PdfSourceDTO;

interface SubjectsTemplateRendererInterface
{
    public function render(StudentSubjectsDTO $student): PdfSourceDTO;
}
