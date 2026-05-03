<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Files;

use Goralys\App\HTTP\Files\Data\FileDTO;
use Goralys\App\HTTP\Files\Data\UploadedFileDTO;
use Goralys\App\HTTP\Files\Interface\FileExtractor;
use Goralys\App\HTTP\Files\Interface\FileMover;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\GoralysRuntimeException;

final class GoralysFileManager
{
    /**
     * @var UploadedFileDTO[]
     */
    private array $uploads = [];
    private FileMover $mover;
    private FileExtractor $extractor;
    private LoggerInterface $logger;

    /**
     * Initializes the file array ($uploads) used by the file manager.
     * @param FileDTO[] $files The files to manage.
     * @param FileMover $mover The injected file mover.
     * @param FileExtractor $extractor The injected file extractor (zip files only)
     * @param LoggerInterface $logger The injected logger.
     * @throws GoralysRuntimeException If an invalid file is found.
     */
    public function __construct(array $files, FileMover $mover, FileExtractor $extractor, LoggerInterface $logger)
    {
        foreach ($files as $inputName => $file) {
            if ($file instanceof FileDTO) {
                $upload = $file->toUploadedFile();
            } elseif ($file instanceof UploadedFileDTO) {
                $upload = $file;
            } else {
                throw new GoralysRuntimeException("Invalid file type");
            }

            $this->uploads[$inputName] = $upload;
        }

        $this->mover = $mover;
        $this->logger = $logger;
        $this->extractor = $extractor;
    }

    /**
     * Retrieves a file inside the upload array, returns null if the file could not be found.
     * @param string $fileName The name of the file to retrieve.
     * @return ?UploadedFileDTO The information of the file.
     */
    public function get(string $fileName): ?UploadedFileDTO
    {
        return $this->uploads[$fileName] ?? null;
    }

    /**
     * Moves a given file.
     * @param string $fileName The file to move.
     * @param string $destination The destination of the file
     * @return bool If the move succeded or not.
     */
    public function move(string $fileName, string $destination): bool
    {
        $file = $this->get($fileName);

        if ($file === null) {
            $this->logger->debug(LoggerInitiator::CORE, "File: " . $fileName . " does not exists");
            return false;
        }

        return $this->mover->move($file->tmpPath, $destination);
    }

    /**
     * Extracts a zip file
     * @param UploadedFileDTO $file The file to extract.
     * @param string $destination The destination folder where the file will be extracted.
     * @return void
     */
    public function extract(UploadedFileDTO $file, string $destination): void
    {
        $this->extractor->extract($file, $destination);
    }
}
