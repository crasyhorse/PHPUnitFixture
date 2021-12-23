<?php

namespace CrasyHorse\Testing;

use Adbar\Dot;
use CrasyHorse\Testing\Exceptions\InvalidConfigurationException;

/**
 * The default configuration and the public method config to read a configuration.
 *
 * @author Florian Weidinger
 */
trait Config
{
    /**
     * Default configuration. This is used by the Fixture class if the use does not
     * define its own configuration.
     *
     * @var array
     */
    protected $configuration = [

        /*
        |-------------------------------------------------------------------------|
        | Sources                                                                 |
        |-------------------------------------------------------------------------|
        |                                                                         |
        | Here you may declare the source directories where your fixture files    |
        | are. You may configure as many sources as you need.                     |
        |                                                                         |
        */
        'sources' => [
            'default' => [
                'driver' => 'local',
                'rootpath' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'filesystem' . DIRECTORY_SEPARATOR. 'data' . DIRECTORY_SEPARATOR,
                'default_file_extension' => 'json'
            ]
        ]
    ];

    /**
     * This array is used as validation schema to check that every adapter has the
     * fields driver and rootpath.
     *
     * @var array
     */
    protected $validationSchema = [
        'driver' => '',
        'rootpath' => '',
        'default_file_extension' => ''
    ];

    /**
     * Wrapper for Dot::get to return the configuration.
     *
     * @param string $name The name of the configuration object to return
     *
     * @return mixed
     */
    public function config(string $name)
    {
        $dot = new Dot($this->configuration);
        return $dot->get($name);
    }

    /**
     * Simple validation function for the configuration object.
     *
     * @param array $config The incoming configuration object
     *
     * @return array
     *
     * @throws \CrasyHorse\Testing\Exceptions\InvalidConfigurationException
     */
    protected function validate(array $config): array
    {
        $dot = new Dot($config);

        if (!$dot->get('sources')) {
            throw new InvalidConfigurationException();
        }
        
        foreach ($dot->get('sources') as $source) {
            $diff1 = array_diff_key($this->validationSchema, $source);
            $diff2 = array_diff_key($source, $this->validationSchema);
            $diff = array_merge($diff1, $diff2);

            if (!empty($diff)) {
                throw new InvalidConfigurationException();
            }
        }

        return $config;
    }
}
