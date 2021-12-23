<?php

namespace CrasyHorse\Testing\Loader;

use CrasyHorse\Testing\Loader\LocalLoader;
use CrasyHorse\Testing\Exceptions\LoaderNotFoundException;
use League\Flysystem\FileNotFoundException;

/**
 * This class works as a factory for all kinds of Loader classes. It also manages the usage of
 * the loaders.
 *
 * @author Florian Weidinger
 * @since 0.1.0
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

        $filename = self::fixFileExtension($source['default_file_extension'], $path);
        
        $file = self::load($filename, $source);

        if (empty($file)) {
            throw new FileNotFoundException($path);
        }

        return $file;
    }

    /**
     * Returns false the given loader does not exist in the loader chain.
     *
     * @param string $loader The loader to be used to read the fixture
     *
     * @return bool
     */
    protected static function checkIfSelectedLoaderExists(string $loader): bool
    {
        return array_key_exists($loader, self::$loaderChain);
    }

    /**
     * If $path does not have a file extension the default file extension set in the
     * configuration is added to $path.
     *
     * @param string $defaultFileExtension The default file extension from the config object
     * @param string $path The filename to check
     *
     * @return string
     */
    protected static function fixFileExtension(string $defaultFileExtension, string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return $extension ? $path : $path . ".{$defaultFileExtension}";
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
