<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit\Config;

use Adbar\Dot;

/**
 * Provides methods for creating specific configurations for specific test cases.
 *
 * @author Florian Weidinger
 * @since
 */
trait CreateConfiguration
{
    /**
     * Returns a standard configuration with all optional attributes.
     *
     * @return array
     */
    private function getConfiguration(): array
    {
        return [
            'loaders' => [
                'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
            ],
            'readers' => [
                'application/json' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
                '*/*' => '\\CrasyHorse\\Testing\\Reader\BinaryReader'
            ],
            'sources' => [
                'default' => [
                    'driver' => 'Local',
                    'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
                    'default_file_extension' => 'json',
                    'encode' => [
                        [
                            'mime-type' => '*/*',
                            'encoder' => 'base64'
                        ]
                    ]
                ],
            ],
        ];
    }

    /**
     * Returns a standard configuration and enabled the user to change parts of it.
     *
     * @param string $argument The argument to change (Array-Dot-Notation)
     * @param mixed $value The value to set
     *
     * @example
     *  $config = $this->createConfiguration('loaders', [
     *       '' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
     *  ]);
     *
     * @return array
     */
    private function createConfiguration(string $argument, $value): array
    {
        $config = $this->getConfiguration();
        $dot = new Dot($config);

        $dot->set($argument, $value);

        return $dot->all();
    }

    /**
     * Adds a value of the given property in the configuration.
     *
     * @param string $argument The property that should be added (Array-Dot-Notation)
     * @param mixed $value The argument's value
     * @param array $config The configuration to use for this
     *
     * @example
     *  $config = $this->getconfiguration();
     *  $config = $this->addProperty('sources.alternative', $config);
     *
     * @return array
     */
    private function addProperty(string $argument, $value, array $config): array
    {
        $dot = new Dot($config);

        $dot->add($argument, $value);

        return $dot->all();
    }

    /**
     * Clears the value of the given property in the configuration.
     *
     * @param string $argument The property whose value should be cleared (Array-Dot-Notation)
     * @param array $config The configuration to use for this
     *
     * @example
     *  $config = $this->getconfiguration();
     *  $config = $this->clearProperty('loaders', $config);
     *
     * @return array
     */
    private function clearProperty(string $argument, array $config): array
    {
        $dot = new Dot($config);

        $dot->clear($argument);

        return $dot->all();
    }

    /**
     * Deletes a property from the given configuration
     *
     * @param string $argument The property that should be deleted (Array-Dot-Notation)
     * @param array $config The configuration to use for this operation
     *
     * @example
     *  $config = $this->getconfiguration();
     *  $config = $this->deleteProperty('loaders', $config);
     *
     * @return array
     */
    private function deleteProperty(string $argument, array $config): array
    {
        $dot = new Dot($config);

        $dot->delete($argument);

        return $dot->all();
    }
}
