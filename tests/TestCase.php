<?php

namespace CrasyHorse\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * A default configuration object used by most specs.
     *
     * @var array
     */
    protected $configuration;

    public function setUp(): void
    {
        parent::setUp();

        $this->configuration = [
            'loaders' => [
                'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
            ],
            'encoders' => [
                'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
            ],
            'readers' => [
                'Local' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
                'Binary' => '\\CrasyHorse\\Testing\\Reader\BinaryReader'
            ],
            'sources' => [
                'default' => [
                    'driver' => 'Local',
                    'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
                    'default_file_extension' => 'json',
                ],
                'alternative' => [
                    'driver' => 'Local',
                    'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'alternative']),
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
}
