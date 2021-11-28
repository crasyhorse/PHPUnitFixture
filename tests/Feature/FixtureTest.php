<?php

namespace CrasyHorse\Tests\Feature;

use CrasyHorse\Testing\Config;
use CrasyHorse\Testing\Fixture;
use CrasyHorse\Tests\TestCase;

class FixtureTest extends TestCase
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
                    'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'filesystem', 'data']),
                    'default_file_extension' => 'json',
                ],
                'alternative' => [
                    'driver' => 'local',
                    'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'filesystem', 'alternative']),
                    'default_file_extension' => 'json',
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function source_sets_object_from_where_the_fixture_should_be_loaded(): void
    {
        $fixture = new Fixture($this->configuration);

        $fixture->source('alternative');
        $fixture->fixture('fixture-003.json');
        $actual = $fixture->toArray();

        $expected = [
            'data' => [
                [
                    'key' => 'FIXTURE-003',
                    'text' => 'Once again a sample text!',
                    'status' => 'open',
                    'updated' => '2021-10-27 10:37:14.0',
                ],
            ],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function fixture_loads_a_single_fixture_file(): void
    {
        $fixture = new Fixture($this->configuration);

        $fixture->fixture('fixture-001.json');
        $actual = $fixture->toArray();

        $expected = [
            'data' => [
                [
                    'key' => 'FIXTURE-001',
                    'text' => 'This is a sample text!',
                    'status' => 'working',
                    'updated' => '2021-10-27 10:35:45.0',
                ],
            ],
        ];

        $this->assertEquals($expected, $actual);
        $this->assertCount(1, $actual['data']);
    }

    /**
     * @test
     */
    public function fixture_loads_a_list_of_fixtures(): void
    {
        $fixture = new Fixture($this->configuration);

        $fixture->fixture(['fixture-001.json', 'fixture-002.json']);
        $actual = $fixture->toArray();

        $expected = [
            'data' => [
                [
                    'key' => 'FIXTURE-001',
                    'text' => 'This is a sample text!',
                    'status' => 'working',
                    'updated' => '2021-10-27 10:35:45.0',
                ],
                [
                    'key' => 'FIXTURE-002',
                    'text' => 'This is another sample text!',
                    'status' => 'stopped',
                    'updated' => '2021-10-27 10:36:27.0',
                ],
            ],
        ];

        $this->assertEquals($expected, $actual);
        $this->assertCount(2, $actual['data']);
    }

    /**
     * @test
     */
    public function fixture_loads_a_very_large_fixture_file(): void
    {
        $fixture = new Fixture($this->configuration);

        $fixture->fixture('fixture-003.json');
        $actual = $fixture->toArray();
        $this->assertCount(1, $actual['data']);
        $this->assertArrayHasKey('0', $actual['data']);
        $this->assertArrayHasKey('text', $actual['data'][0]);
        $this->assertEquals(1319588, mb_strlen($actual['data'][0]['text']));
        $this->assertLessThanOrEqual(32768, (memory_get_peak_usage(true) / 1024));
    }

    /**
     * @test
     */
    public function tojson_returns_the_json_representation_of_content(): void
    {
        $fixture = new Fixture($this->configuration);

        $fixture->fixture('fixture-001.json');
        $actual = $fixture->toJson();

        $expected = '{"data":[{"key":"FIXTURE-001","text":"This is a sample text!","status":"working","updated":"2021-10-27 10:35:45.0"}]}';

        $this->assertEquals($expected, $actual);
    }
}
