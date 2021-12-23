<?php

namespace CrasyHorse\Testing\Loader;

use \League\Flysystem\Adapter\Local;
use \League\Flysystem\Filesystem;
use CrasyHorse\Testing\Loader\AbstractLoader;
use League\Flysystem\FileNotFoundException;

/**
 * Loads files from the local filesystem. It uses League\Flysystem\Adapter\Local to get
 * things done.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class LocalLoader extends AbstractLoader
{
    const TYPE='Local';

    /**
     * @inheritdoc
     */
    public function load(string $path, array $source)
    {
        try {
            if (strToLower($source['driver']) === strToLower($this::TYPE)) {
                $this->filesystem = $this->initLoader($source['rootpath']);

                return $this->readFile($path);
            }
        } catch (FileNotFoundException $e) {
            if ($this->filepathIsLocal($path) === true) {
                throw new FileNotFoundException($path);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    protected function initLoader(string $path): FileSystem
    {
        $adapter = new Local($path);

        return new Filesystem($adapter);
    }

    public function filepathIsLocal(string $path): bool
    {
        return in_array(dirname($path), [DIRECTORY_SEPARATOR, '.', '..']);
    }
}
