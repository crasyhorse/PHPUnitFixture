<?php

namespace CrasyHorse\Testing\Loader;

use CrasyHorse\Testing\Exceptions\LoaderNotFoundException;
use CrasyHorse\Testing\Loader\File;
use CrasyHorse\Testing\Config\Config;
use ReflectionClass;
use ReflectionException;

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
    private static $loaders;

    /**
     * Instantiates the loader chain and starts loading a file.
     *
     * @param string $path The path to the file to load. It is relative to the $sources['root_path].
     *
     * @param string $source The name of the Config.source object to use for loading the fixture
     *
     * @return \CrasyHorse\Testing\Loader\File
     *
     * @throws \CrasyHorse\Testing\Exceptions\LoaderNotFoundException
     */
    public static function loadFixture(string $path, string $source): File
    {
        self::instantiateLoader();

        $defaultFileExtension = Config::getInstance()->get("sources.{$source}.default_file_extension");
        $filename = self::fixFileExtension($defaultFileExtension, $path);

        return self::load($filename, $source);
    }

    /**
     * If $path does not have a file extension the default file extension from the
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
     * Adds all available Loader classes to the loader chain.
     *
     * @return void
     *
     */
    protected static function instantiateLoader(): void
    {
        $loaders = Config::getInstance()->get('loaders');

        try {
            foreach ($loaders as $key => $loader) {
                self::$loaders[$key] = (new ReflectionClass($loader))->newInstanceArgs();
            }
        } catch (ReflectionException $e) {
            throw new LoaderNotFoundException($key);
        }
    }

    /**
     * Uses the informationen about the driver from the $source object to
     * select the correct Loader for loading the fixture.
     *
     * @param string $path The path to the file to load. It is relative to the $sources['root_path].
     *
     * @param string $source The name of the Config.source object to use for loading the fixture
     *
     * @return \CrasyHorse\Testing\Loader\File|null
     *
     */
    protected static function load(string $path, string $source)
    {
        $loader = Config::getInstance()->get("sources.{$source}.driver");

        return self::$loaders[$loader]->load($path, $source);
    }
}
