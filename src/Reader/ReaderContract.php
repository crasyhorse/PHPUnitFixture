<?php

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Loader\File;

/**
 * Defines all necessary methods for classes of type Reader.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
interface ReaderContract
{
    /**
     * Reads the contents of a {@link File}. The file is getting processor by the responsible Reader class.
     * The responsible reader is determined by the file type.
     *
     * @param \CrasyHorse\Testing\Loader\File $file The file to read.
     *
     * @return array|null
     */
    public function read(File $file);

    /**
     * Uses mime types or regex to validate whether the Reader is responsible for this type
     * of content or not.
     *
     * @param string $content The content string to validate
     *
     * @return bool
     */
    public function isValid(string $content): bool;
}
