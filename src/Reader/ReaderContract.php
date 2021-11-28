<?php

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Loader\File;

interface ReaderContract
{
    
    /**
     * Reads the contents of a {@link File}. The file is getting processor by the responsible Reader class.
     * The responsible reader is determined by the file type.
     *
     * @param string $path The path to the file to read. It is relative to the $sources['rootpath].
     *
     * @param array $source Configuration object that tells us which Loader to use and where to find the file to read.
     *
     * @return mixed
     *
     */
    public function read(File $file);
}
