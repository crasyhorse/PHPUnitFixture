<?php

namespace CrasyHorse\Testing\Loader;

use CrasyHorse\Testing\Loader\LoaderContract;
use League\Flysystem\Filesystem;
use CrasyHorse\Testing\Loader\File;
use League\Flysystem\Adapter\AbstractAdapter;

/**
 * Abstract base class for all Loader classes. It defines the method "readFile" used
 * by every Loader to get read access to the fixture file. It also defines the
 * abstract method "initLoader" every Loader has to implement.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
abstract class AbstractLoader implements LoaderContract
{
    /**
     * The Flysystem\Filesystem used to read the file
     *
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * Initializes the League\Flysystem\Filesystem.
     *
     * @param string $path The absolute path to the file the loader should load.
     *
     * @return \League\Flysystem\Filesystem
     */
    abstract protected function initLoader(string $path): FileSystem;

    /**
     * Reads the file and its meta data.
     *
     * @param string $path The absolute path to the file the loader should load.
     *
     * @return \CrasyHorse\Testing\Loader\File
     */
    public function readFile(string $path): File
    {
        /** @var string $content */
        $content = $this->filesystem->read($path);

        /** @var string $mimetype */
        $mimetype = $this->filesystem->getMimetype($path);

        /** @var int $timestamp */
        $timestamp = $this->filesystem->getTimestamp($path);

        /** @var float $size */
        $size = $this->filesystem->getSize($path);

        /** @var  \League\Flysystem\Adapter\AbstractAdapter $adapter */
        $adapter = ($this->filesystem->getAdapter());
        $pathPrefix = $this->getPathPrefix($adapter);

        return new File($path, $content, $pathPrefix, $size, $mimetype, $timestamp);
    }

    /**
     * Get the path prefix.
     *
     * @param \League\Flysystem\Adapter\AbstractAdapter $adapter The Flysystem adapter in use.
     *
     * @return string|null
     */
    protected function getPathPrefix(AbstractAdapter $adapter)
    {
        return $adapter->getPathPrefix();
    }
}
