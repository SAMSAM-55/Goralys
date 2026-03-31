<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\Doc\PDF\Data;

/* DTO to represent a source for a PDF file that will be later exported to a true PDF file */
class PdfSourceDTO
{
    private string $HTML;
    private string $CSS;

    public function setHTML(string $HTML): void
    {
        $this->HTML = $HTML;
    }

    public function setCSS(string $CSS): void
    {
        $this->CSS = $CSS;
    }

    public function getHTML(): string
    {
        return $this->HTML;
    }

    public function getCSS(): string
    {
        return $this->CSS;
    }
}
