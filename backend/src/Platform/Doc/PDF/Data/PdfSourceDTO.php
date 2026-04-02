<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\Doc\PDF\Data;

/* DTO to represent a source for a PDF file that will be later exported to a true PDF file */
readonly class PdfSourceDTO
{
    public function __construct(
        public string $HTML = "",
        public string $CSS = ""
    ) {
    }
}
