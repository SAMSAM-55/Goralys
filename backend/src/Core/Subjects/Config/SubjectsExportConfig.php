<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\Core\Subjects\Config;

use Goralys\Core\Subjects\Data\PathwayDTO;

/**
 * Configuration class for the subject export process.
 */
final class SubjectsExportConfig
{
    public const string ASSETS_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Assets' . DIRECTORY_SEPARATOR .
    'Template' . DIRECTORY_SEPARATOR;
    public const string TEMPLATE_SOURCE_PATH = self::ASSETS_PATH . 'main.html';
    public const string TEMPLATE_STYLES_PATH = self::ASSETS_PATH . 'style.css';
    public const string EXPORT_BASE_NAME     = 'FICHE_GO-';

    /**
     * @return PathwayDTO[]
     */
    public static function getTechnologicalPathways(): array
    {
        return [
            new PathwayDTO(
                full: 'Sciences et Technologies du Management et de la Gestion',
                detectPattern: 'STMG'
            )
        ];
    }
}
