<?php

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Reader\AbstractReader;
use CrasyHorse\Testing\Loader\File;

/**
 *
 * @author Florian Weidinger
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
    protected function doRead(File $file)
    {
        if (strToLower($file->getMimeType() ?? '') === strToLower($this::TYPE)) {
        }

        return null;
    }
}
