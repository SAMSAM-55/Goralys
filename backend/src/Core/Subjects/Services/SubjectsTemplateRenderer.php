<?php

namespace Goralys\Core\Subjects\Services;

use Goralys\Core\Subjects\Config\SubjectsExportConfig;
use Goralys\Core\Subjects\Data\StudentSubjectsDTO;
use Goralys\Core\Subjects\Interfaces\SubjectsTemplateRendererInterface;
use Goralys\Platform\Doc\PDF\Data\PdfSourceDTO;
use Goralys\Platform\Doc\PDF\Interfaces\PdfExporterInterface;

class SubjectsTemplateRenderer implements SubjectsTemplateRendererInterface
{
    private PdfExporterInterface $exporter;
    private SubjectsExportConfig $config;

    public function __construct(PdfExporterInterface $exporter, SubjectsExportConfig $config)
    {
        $this->exporter = $exporter;
        $this->config = $config;
    }

    public function render(StudentSubjectsDTO $student): PdfSourceDTO
    {
        if (count($student->getSubjects()) < 2) {
            throw new \InvalidArgumentException(
                "Student {$student->studentName} must have at least 2 subjects. Got: "
                . print_r($student->getSubjects(), true)
            );
        }

        $pdf = $this->exporter->create();
        $html = file_get_contents($this->config::TEMPLATE_SOURCE_PATH);
        $css = file_get_contents($this->config::TEMPLATE_STYLES_PATH);

        $pos = strpos($student->studentName, " ");
        $firstname = $pos !== false ? substr($student->studentName, 0, $pos) : $student->studentName;
        $lastname  = $pos !== false ? substr($student->studentName, $pos + 1) : "";

        $colMm = [
            'label'  => 22,
            'intitu' => 100,
            'spe'    => 45,
            'trans'  => 35,
        ];

        // Compute percentages that sum to exactly 100%
        $totalMm = array_sum($colMm);
        $pct = [];
        $assigned = 0;
        $keys = array_keys($colMm);
        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $pct[$k] = round(100 - $assigned, 2);
            } else {
                $p = round($colMm[$k] / $totalMm * 100, 2);
                $pct[$k] = $p;
                $assigned += $p;
            }
        }

        $pathway = "générale";

        foreach ($this->config::getTechnologicalPathways() as $p) {
            foreach ($student->getSubjects() as $subject) {
                if ($subject->topicCode && str_contains($subject->topicCode, $p->getDetectPattern())) {
                    $pathway = "technologique - " . $p->getFull();
                    break 2;
                }
            }
        }

        // CSS block injected alongside the main stylesheet
        $css .= PHP_EOL . PHP_EOL . "
            .questions-table { width: {$totalMm}mm; }
            .qt-col1 { width: {$pct['label']}%; }
            .qt-col2 { width: {$pct['intitu']}%; }
            .qt-col3 { width: {$pct['spe']}%; }
            .qt-col4 { width: {$pct['trans']}%; }
            ";

        $replacements = [
            '{{nom}}'    => $lastname,
            '{{prenom}}' => $firstname,
            '{{serie}}'  => $pathway,
            '{{spe1}}'   => $student->getSubjects()[0]->speciality,
            '{{q1}}'     => $student->getSubjects()[0]->subject,
            '{{spe2}}'   => $student->getSubjects()[1]->speciality,
            '{{q2}}'     => $student->getSubjects()[1]->subject,
            '{{year}}'   => date("Y"),
        ];

        $html = strtr($html, $replacements);

        $this->exporter->setSource($pdf, $html);
        $this->exporter->setStyle($pdf, $css);

        return $pdf;
    }
}
