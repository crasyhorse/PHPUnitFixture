<?php

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Testing\Fixture;
use CrasyHorse\Tests\TestCase;
use CrasyHorse\Testing\Exceptions\SourceNotFoundException;
use CrasyHorse\Testing\Exceptions\InvalidConfigurationException;

class FixtureTest extends TestCase
{
    /**
     * @test
     * @testdox Initializint a Fixture throws an exception if a configuration object has no default source
     */
    public function throws_an_exception_if_a_configuration_object_has_no_default_source(): void
    {
        $this->expectException(SourceNotFoundException::class);
        $this->expectExceptionMessage('Could not find the default source. Please create it.');
        
        $configuration = [
            'sources' => [
                'alternative' => [
                    'driver' => 'local',
                    'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'filesystem', 'alternative']),
                    'default_file_extension' => 'json',
                ],
            ],
        ];

        $fixture = new Fixture($configuration);
    }

    public function malformed_configuratin_provider(): array
    {
        return [
            'a malformed default source is used.' => [
                [
                    'sources' => [
                        'default' => [
                            'diver' => 'local',
                            'grootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'filesystem', 'data']),
                            'default_file_extension' => 'json',
                        ]
                    ],
                ],
            ],

            'the "sources" element in configuration is missing.' => [
                [
                    'destinations' => [
                        'default' => [
                            'driver' => 'local',
                            'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'filesystem', 'data']),
                            'default_file_extension' => 'json',
                        ]
                    ],
                ]
            ],

            'an additional element is inserted in the configuration.' => [
                [
                    'sources' => [
                        'default' => [
                            'driver' => 'local',
                            'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'filesystem', 'data']),
                            'default_file_extension' => 'json',
                            'additional_element' => true
                        ]
                    ],
                ]
            ]
        ];
    }
    
    /**
     * @test
     * @dataProvider malformed_configuratin_provider
     * @testdox Initializint a Fixture throws an exception if $_dataName
     */
    public function throws_an_exception_if_a_malformed_configuration_object_is_used_to_initialize_the_fixture(array $configuration): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration object is malformed. Please check it!');

        $fixture = new Fixture($configuration);
    }

    /**
     * @test
     */
    public function source_throws_an_exception_if_a_configuration_object_does_not_contain_the_selected_source(): void
    {
        $this->expectException(SourceNotFoundException::class);
        $this->expectExceptionMessage('The selected source could not be found. Please configure it or use default source.');
        
        $configuration = [
            'sources' => [
                'default' => [
                    'driver' => 'local',
                    'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'filesystem', 'data']),
                    'default_file_extension' => 'json',
                ]
            ],
        ];

        $fixture = new Fixture($configuration);

        $fixture->source('alternative');
    }
}
