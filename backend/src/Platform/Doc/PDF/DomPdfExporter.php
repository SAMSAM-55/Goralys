<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Platform\Doc\PDF;

use Dompdf\Dompdf;
use Dompdf\Options;
use Goralys\Platform\Doc\PDF\Data\PdfSourceDTO;
use Goralys\Platform\Doc\PDF\Interfaces\PdfExporterInterface;

class DomPdfExporter implements PdfExporterInterface
{
    /**
     * @return PdfSourceDTO
     */
    public function create(): PdfSourceDTO
    {
        return new PdfSourceDTO();
    }

    /**
     * @param PdfSourceDTO $pdf
     * @param string $path
     * @param string $basePath
     * @return void
     */
    public function export(PdfSourceDTO $pdf, string $path, string $basePath): void
    {
        $finalSource = str_replace(
            '</head>',
            "<style>\n{$pdf->getCSS()}\n</style>\n</head>",
            $pdf->getHTML()
        );

        // --- Dompdf options ---
        $options = new Options();
        $options->set('isRemoteEnabled', true);     // allow images
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Marianne');   // matches @font-face name
        $options->set('chroot', $basePath);

        $dompdf = new Dompdf($options);

        // Important: base path for images/fonts
        $dompdf->setBasePath($basePath);

        // Load HTML
        $dompdf->loadHtml($finalSource, 'UTF-8');

        // Paper size
        $dompdf->setPaper('A4');

        // Render PDF
        $dompdf->render();

        // Save to file
        file_put_contents($path, $dompdf->output());
    }

    /**
     * @param PdfSourceDTO $pdf
     * @param string $source
     * @return void
     */
    public function setSource(PdfSourceDTO $pdf, string $source): void
    {
        $pdf->setHTML($source);
    }

    /**
     * @param PdfSourceDTO $pdf
     * @param string $style
     * @return void
     */
    public function setStyle(PdfSourceDTO $pdf, string $style): void
    {
        $pdf->setCSS($style);
    }
}
