<?php

namespace CrasyHorse\Testing\Loader;

/**
 * This interface defines the method "load". That is the main method all
 * Loader classes have to implement.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
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
