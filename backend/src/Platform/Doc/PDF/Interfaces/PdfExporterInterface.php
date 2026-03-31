<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\Doc\PDF\Interfaces;

use Goralys\Platform\Doc\PDF\Data\PdfSourceDTO;

interface PdfExporterInterface
{
    public function create(): PdfSourceDTO;
    public function export(PdfSourceDTO $pdf, string $path, string $basePath): void;

    public function setSource(PdfSourceDTO $pdf, string $source): void;
    public function setStyle(PdfSourceDTO $pdf, string $style): void;
}
