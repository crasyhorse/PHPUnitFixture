<?php

namespace CrasyHorse\Testing\Loader;

use CrasyHorse\Testing\Loader\File;

interface LoaderContract
{
    /**
     * Returns the data of a fixture or false if the file could not be found.
     *
     * @param string $path The absolute path to the file
     *
     * @param array $source The type of the League\Flysystem\Adapter to use to load the file
     *
     * @return \CrasyHorse\Testing\Loader\File|null
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function load(string $path, array $source);
}
