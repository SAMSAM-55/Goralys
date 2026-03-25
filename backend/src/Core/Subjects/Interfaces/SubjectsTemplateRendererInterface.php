<?php

namespace Goralys\Core\Subjects\Interfaces;

use Goralys\Core\Subjects\Data\StudentSubjectsDTO;
use Goralys\Platform\Doc\PDF\Data\PdfSourceDTO;

interface SubjectsTemplateRendererInterface
{
    public function render(StudentSubjectsDTO $student): PdfSourceDTO;
}
