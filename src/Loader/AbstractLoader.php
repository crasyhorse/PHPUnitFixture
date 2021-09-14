<?php

namespace CrasyHorse\Testing\Loader;

use CrasyHorse\Testing\Loader\LoaderContract;
use \League\Flysystem\Filesystem;
use CrasyHorse\Testing\Loader\File;
use League\Flysystem\Adapter\AbstractAdapter;

/**
 *
 * @author Florian Weidinger
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
        $result = $this->filesystem->read($path);
        $content = $result ? $result : '';

        $result = $this->filesystem->getMimetype($path);
        $mimetype = $result ? $result : null;

        $result = $this->filesystem->getTimestamp($path);
        $timestamp = $result ? $result : 0;

        $result = $this->filesystem->getSize($path);
        $size = $result ? $result : 0.0;

        $adapter = ($this->filesystem->getAdapter());
        $pathPrefix = $this->getPathPrefix($adapter);
        
        return new File($path, $content, $pathPrefix, $size, $mimetype, $timestamp);
    }

    /**
     *
     * @return string|null
     */
    protected function getPathPrefix(AbstractAdapter $adapter)
    {
        return $adapter->getPathPrefix();
    }
}
