<?php

declare(strict_types=1);

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Loader\File;

/**
 * This class is responsible for reading and parsing fixture files with
 * binary content. It is some kind of generic reader for all kinds of
 * binary files that do not have a more specific reader class.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
class BinaryReader extends AbstractReader
{
    /**
     * The mime type the Reader class is responsible for.
     *
     * @var string
     */
    public const MIME_TYPE='*/*';

    /**
     * {@inheritdoc}
     */
    public function isValid(string $content): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doRead(File $file): array
    {
        return [$file->getContent()];
    }
}
