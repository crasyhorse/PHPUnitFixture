<?php

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Reader\ReaderContract;
use CrasyHorse\Testing\Loader\File;

/**
 * Abstract base class for all Reader classes. It implements the "read" method
 * defined by CrasyHorse\Testing\Reader\ReaderContract and it also provides
 * the abstract method "doRead" every Reader has to implement.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
abstract class AbstractReader implements ReaderContract
{
    /**
     * Executes steps needed to initialize a Reader object.
     *
     * @return void
     */
    abstract protected function initReader(): void;

    /**
     * @inheritdoc
     */
    public function read(File $file)
    {
        $this->initReader();
        $content = $this->doRead($file);

        return $content;
    }

    /**
     * Does all the work necessary work to read a file (e. g. decompress it or convert it to JSON)
     *
     * @param \CrasyHorse\Testing\Loader\File $file
     *
     * @return array
     */
    abstract protected function doRead(File $file): array;
}
