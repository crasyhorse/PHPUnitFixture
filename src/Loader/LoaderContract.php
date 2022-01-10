<?php

namespace CrasyHorse\Testing\Loader;

use CrasyHorse\Testing\Loader\File;

/**
 * Defines all necessary methods for classes of type Loader.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
interface LoaderContract
{
    /**
     * Returns the data of a fixture.
     *
     * @param string $path The absolute path to the file
     *
     * @param string $source The name of the Config.source object to use for loading the fixture
     *
     * @return \CrasyHorse\Testing\Loader\File
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function load(string $path, string $source): File;
}
