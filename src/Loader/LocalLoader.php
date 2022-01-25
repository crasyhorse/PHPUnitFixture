<?php

namespace CrasyHorse\Testing\Loader;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use CrasyHorse\Testing\Loader\AbstractLoader;
use League\Flysystem\FileNotFoundException;
use CrasyHorse\Testing\Config\Config;

/**
 * Loads files from the local filesystem. It uses League\Flysystem\Adapter\Local to get
 * things done.
 *
 * The type of this loader is 'Local'
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class LocalLoader extends AbstractLoader
{
    /**
     * Returns the data of a fixture.
     *
     * @param string $path The absolute path to the file
     *
     * @param string $source The name of the Config.source object to use for loading the fixture
     *
     * @param \CrasyHorse\Testing\Config\Config $configuration
     *
     * @return \CrasyHorse\Testing\Loader\File|null
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function load(string $path, string $source, Config $configuration)
    {
        try {
            /** @var string $rootPath */
            $rootPath = $configuration->get("sources.{$source}.root_path");
            $this->filesystem = $this->initLoader($rootPath);

            return $this->readFile($path);
        } catch (FileNotFoundException $e) {
            if ($this->filePathIsLocal($path) === true) {
                throw new FileNotFoundException($path);
            }

            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    protected function initLoader(string $path): FileSystem
    {
        $adapter = new Local($path);

        return new Filesystem($adapter);
    }

    /**
     * Returns true if a file path is identified as a local Linux or Windows
     * path.
     *
     * @param string $path The path to be checked
     *
     * @return bool
     */
    private function filePathIsLocal(string $path): bool
    {
        return in_array(dirname($path), [DIRECTORY_SEPARATOR, '.', '..']);
    }
}
