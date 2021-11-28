<?php

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Loader\Loader;
use CrasyHorse\Testing\Reader\JsonReader;
use CrasyHorse\Testing\Reader\UbJsonReader;
use League\Flysystem\FileNotFoundException;

/**
 * This class works as a factory for all kinds of Reader classes. It also manages the usage of
 * the readers.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class Reader
{
    /**
     * An object holding the attributes of the file to read.
     *
     * @var \CrasyHorse\Testing\Loader\File|null
     */
    protected static $file;

    /**
     * A list of all available reader classes.
     *
     * @var \CrasyHorse\Testing\Reader\ReaderContract[]
     */
    protected static $readers;

    /**
     * Notifies the reader objects that there is a file to read.
     *
     * @param string $path The path to the file to read. It is relative to the $sources['rootpath].
     *
     * @param array $source Configuration object that tells us which Loader to use and where to find the file to read.
     *
     * @return mixed
     *
     */
    public static function read(string $path, array $source)
    {
        self::instantiateReader();

        self::$file = Loader::loadFixture($path, $source);
        $content = null;

        if (empty(self::$file)) {
            throw new FileNotFoundException($path);
        }

        foreach (self::$readers as $reader) {
            $result = $reader->read(self::$file);

            if (!empty($result)) {
                $content = $result;
            }
        }
        
        return $content;
    }

    /**
     * Adds all available reader classes to the readers list.
     *
     * @return void
     *
     */
    protected static function instantiateReader(): void
    {
        self::$readers[] = new JsonReader();
        self::$readers[] = new UbJsonReader();
    }
}
