<?php

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Reader\AbstractReader;
use CrasyHorse\Testing\Loader\File;

/**
 * This class is responsible for reading and parsing fixture files of
 * mime type "application/ubjson".
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class UbJsonReader extends AbstractReader
{
    const TYPE='application/json';

    /**
     * @inheritdoc
     */
    protected function initReader(): void
    {
    }

    /**
     * @inheritdoc
     */
    protected function doRead(File $file): array
    {
        if (strToLower($file->getMimeType() ?? '') === strToLower($this::TYPE)) {
        }

        return [];
    }
}
