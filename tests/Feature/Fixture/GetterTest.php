<?php

namespace CrasyHorse\Tests\Feature\Fixture;

use CrasyHorse\Tests\TestCase;
use CrasyHorse\Testing\Config;
use CrasyHorse\Testing\Fixture;

class GetterTest extends TestCase
{
    use Config;

    /**
     * The main configuration object.
     *
     * @var array
     */
    protected $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->configuration = [
            'sources' => [
                'default' => [
                    'driver' => 'local',
                    'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'filesystem', 'data']),
                    'default_file_extension' => 'json',
                ],
                'alternative' => [
                    'driver' => 'local',
                    'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'filesystem', 'alternative']),
                    'default_file_extension' => 'json',
                ],
            ],
        ];
    }

    public function fixture_provider(): array
    {
        return [
            'a single fixture file in use' => [
                [
                    'fixture-001.json'
                ],
                [
                    'data' => [
                        [
                            'key' => 'FIXTURE-001',
                            'text' => 'This is a sample text!',
                            'status' => 'working',
                            'updated' => '2021-10-27 10:35:45.0'
                        ]
                    ]
                ]
                  
            ],
            'a list of two fixture files in use' => [
                [
                    'fixture-001.json', 'fixture-002.json'
                ],
                [
                    'data' => [
                        [
                            'key' => 'FIXTURE-001',
                            'text' => 'This is a sample text!',
                            'status' => 'working',
                            'updated' => '2021-10-27 10:35:45.0'
                        ],
                        [
                            'key' => 'FIXTURE-002',
                            'text' => 'This is another sample text!',
                            'status' => 'stopped',
                            'updated' => '2021-10-27 10:36:27.0'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
      * @test
      * @dataProvider fixture_provider
      */
    public function executing_get_whith_no_arguments_returns_the_whole_contents_array(array $fixtures, array $expected): void
    {
        $fixture = new Fixture($this->configuration);
        $fixture->fixture($fixtures);

        $actual = $fixture->get();
        $this->assertEquals($expected, $actual);
    }

    public function array_dot_notation_provider(): array
    {
        return [
            'data' => [
                'data',
                'data' => [
                    [
                        'key' => 'FIXTURE-001',
                        'text' => 'This is a sample text!',
                        'status' => 'working',
                        'updated' => '2021-10-27 10:35:45.0'
                    ],
                    [
                        'key' => 'FIXTURE-002',
                        'text' => 'This is another sample text!',
                        'status' => 'stopped',
                        'updated' => '2021-10-27 10:36:27.0'
                    ]
                ]
            ],
            'data.0' => [
                'data.0',
                [
                    'key' => 'FIXTURE-001',
                    'text' => 'This is a sample text!',
                    'status' => 'working',
                    'updated' => '2021-10-27 10:35:45.0'
                ]
            ],
            'data.1.key' => [
                'data.1.key',
                'FIXTURE-002'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider array_dot_notation_provider
     */
    public function executing_get_with_one_single_string_argument_uses_array_dot_notation_to_access_content(string $dotNotation, $expected): void
    {
        $fixture = new Fixture($this->configuration);
        $fixture->fixture(['fixture-001.json', 'fixture-002.json']);

        $actual = $fixture->get($dotNotation);

        $this->assertEquals($expected, $actual);
    }
}
