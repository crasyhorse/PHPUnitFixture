<?php

namespace CrasyHorse\Testing\Reader;

use ArrayObject;
use ArrayIterator;
use CrasyHorse\Testing\Exceptions\NoSuitableReaderFoundException;
use CrasyHorse\Testing\Loader\Loader;
use CrasyHorse\Testing\Config\Config;
use CrasyHorse\Testing\Exceptions\ReaderNotFoundException;
use ReflectionClass;
use ReflectionException;
use CrasyHorse\Testing\Exceptions\SourceNotFoundException;

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
     * @var \CrasyHorse\Testing\Config\Config $configuration
     */
    protected static $configuration;

    /**
     * An object holding the attributes of the file to read.
     *
     * @var \CrasyHorse\Testing\Loader\File|null
     */
    protected static $file;

    /**
     * A list of all available reader classes.
     *
     * @var array
     */
    protected static $readers;

    /**
     * Notifies the reader objects that there is a file to read.
     *
     * @param string $path The path to the file to read. It is relative to the $sources['rootpath].
     *
     * @param string $source The name of the Config.source object to use for loading the fixture
     *
     * @param \CrasyHorse\Testing\Config\Config $configuration
     *
     * @return array<array-key, mixed>|null
     *
     */
    public static function read(string $path, string $source, Config $configuration): array
    {
        self::$configuration = $configuration;

        $sourceObject = self::$configuration->get("sources.{$source}");

        if (empty($sourceObject)) {
            throw new SourceNotFoundException($source);
        }

        self::instantiateReader($source);
        $readerIterator = (new ArrayObject(self::$readers))->getIterator();

        self::$file = Loader::loadFixture($path, $source, $configuration);

        $reader = $readerIterator->current();

        while ($readerIterator->valid() && $reader->isValid(self::$file->getContent()) === false) {
            $readerIterator->next();
            $reader = $readerIterator->current();
        }

        if (empty($reader)) {
            throw new NoSuitableReaderFoundException($path);
        }

        /** @var ReaderContract $reader */
        return $reader->read(self::$file);
    }

    /**
     * Adds all available reader classes to the readers list.
     *
     * @param string $source The name of the Config.source object to use for loading the fixture
     *
     * @return void
     *
     */
    protected static function instantiateReader(string $source): void
    {
        self::$readers = [];
        /** @var array<array-key,class-string> $readers */
        $readers = self::$configuration->get('readers');

        try {
            foreach ($readers as $key => $reader) {
                self::$readers[$key] = (new ReflectionClass($reader))->newInstanceArgs([$source, self::$configuration]);
            }
        } catch (ReflectionException $e) {
            $readerKey = (string) $key ?? 'unknown';
            throw new ReaderNotFoundException($readerKey);
        }
    }
}
