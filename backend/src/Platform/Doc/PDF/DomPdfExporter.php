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

/**
 * Wrapper around the base {@see Dompdf} implementation used to export HTML documents to PDF.
 */
final class DomPdfExporter implements PdfExporterInterface
{
    /**
     * Exports an HTML template to a PDF file.
     * @param PdfSourceDTO $pdf The PDF to export.
     * @param string $path The path to export the PDF to.
     * @param string $basePath The root path for assets used during PDF export.
     * @return void
     */
    public function export(PdfSourceDTO $pdf, string $path, string $basePath): void
    {
        $finalSource = str_replace(
            '</head>',
            "<style>\n$pdf->CSS\n</style>\n</head>",
            $pdf->HTML
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
}
