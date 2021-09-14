<?php

namespace CrasyHorse\Testing\Loader;

use CrasyHorse\Testing\Loader\LocalLoader;
use CrasyHorse\Testing\Exceptions\LoaderNotFoundException;

/**
 * This class works as a factory for all kinds of loader classes. It also manages the usage of
 * the loaders.
 *
 * @author Florian Weidinger
 */
class Loader
{
    /**
     * An associative array of Loader classes
     *
     * @var \CrasyHorse\Testing\Loader\LoaderContract[]
     */
    private static $loaderChain;

    /**
     * Instantiates the loader chain and starts loading a file.
     *
     * @param string $path The path to the file to load. It is relative to the $sources['rootpath].
     *
     * @param array $source Configuration object that tells us which Loader to use and where to find the file to load.
     *
     * @return \CrasyHorse\Testing\Loader\File|null
     *
     * @throws \CrasyHorse\Testing\Exceptions\LoaderNotFoundException
     */
    public static function loadFixture(string $path, array $source)
    {
        self::instantiateLoader();

        if (!self::checkIfSelectedLoaderExists($source['driver'])) {
            throw new LoaderNotFoundException("", 0, null, $source['driver']);
        }

        $file = self::load($path, $source);
    
        return $file;
    }

    protected static function checkIfSelectedLoaderExists(string $driver): bool
    {
        return array_key_exists($driver, self::$loaderChain);
    }

    /**
     * Adds all available loader classes to the loader chain.
     *
     * @return void
     *
     */
    protected static function instantiateLoader(): void
    {
        self::$loaderChain['local'] = new LocalLoader();
    }

    /**
     * Notifies the loader objects that there is a file to load.
     *
     * @param string $path The path to the file to load. It is relative to the $sources['rootpath].
     *
     * @param array $source Configuration object that tells us which Loader to use and where to find the file to load.
     *
     * @return \CrasyHorse\Testing\Loader\File|null
     *
     */
    protected static function load(string $path, array $source)
    {
        $file = null;

        foreach (self::$loaderChain as $loader) {
            $result = $loader->load($path, $source);
            $file = $result ? $result : null;
        }

        return $file;
    }
}
