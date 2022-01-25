<?php

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Testing\Fixture;
use CrasyHorse\Tests\TestCase;
use CrasyHorse\Testing\Exceptions\SourceNotFoundException;
use CrasyHorse\Testing\Exceptions\InvalidArgumentException;
use CrasyHorse\Testing\Exceptions\InvalidConfigurationException;

/**
 * @covers CrasyHorse\Testing\Fixture
 * @covers CrasyHorse\Testing\Exceptions\InvalidArgumentException
 * @covers CrasyHorse\Testing\Exceptions\SourceNotFoundException
 */
class FixtureTest extends TestCase
{
    use FixtureProvider;

    /**
     * @test
     * @group Fixture
     * @testdox source method selects the source from where the fixture should be loaded
     */
    public function source_sets_object_from_where_the_fixture_should_be_loaded(): void
    {
        $fixture = new Fixture($this->configuration);
        $fixture->source('alternative');
        $resolvedContents = $fixture->fixture('fixture-003.json');
        $actual = $resolvedContents->toArray();

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
     * @group Fixture
     */
    public function source_throws_an_exception_if_a_configuration_object_does_not_contain_the_selected_source(): void
    {
        $this->expectException(SourceNotFoundException::class);
        $this->expectExceptionMessage("The selected source 'alternative' could not be found. Please configure it or use the default source.");

        $configuration = [
            'loaders' => [
                'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
            ],
            'readers' => [
                'Local' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
                'Binary' => '\\CrasyHorse\\Testing\\Reader\BinaryReader'
            ],
            'encoders' => [
                'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
            ],
            'sources' => [
                'default' => [
                    'driver' => 'Local',
                    'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'data', 'default']),
                    'default_file_extension' => 'json',
                ]
            ],
        ];

        $fixture = new Fixture($configuration);
        $fixture->source('alternative');
    }

    /**
     * @test
     * @group Fixture
     * @testdox fixture method loads $_dataName
     * @dataProvider fixture_provider
     */
    public function fixture_loads_fixtures($fixtures, $expected, $expectedCounter): void
    {
        $fixture = new Fixture($this->configuration);
        $content = $fixture->fixture($fixtures);
        $actual = $content->toArray();

        $this->assertEquals($expected, $actual);
        $this->assertCount($expectedCounter, $actual['data']);
    }

    /**
     * @test
     * @group Fixture
     */
    public function fixture_method_loads_a_very_large_fixture_from_a_json_file(): void
    {
        $fixture = new Fixture($this->configuration);
        $content = $fixture->fixture('fixture-003.json');
        $actual = $content->toArray();

        $this->assertCount(1, $actual['data']);
        $this->assertArrayHasKey('0', $actual['data']);
        $this->assertArrayHasKey('text', $actual['data'][0]);
        $this->assertEquals(1319588, mb_strlen($actual['data'][0]['text']));
        $this->assertLessThanOrEqual(348160, (memory_get_peak_usage(true) / 1024));
    }

    /**
     * @test
     * @group Fixture
     */
    public function resolveFixture_throws_an_exception_if_the_given_file_name_is_not_a_string_nor_an_array(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A method has received an invalid argument value for one of its parameters. $fixture must be a string or an array!');

        $fixture = new Fixture($this->configuration);
        $content = $fixture->fixture(1);
    }

    /**
     * @test
     * @group Fixture
     */
    public function resolveFixture_throws_an_exception_if_file_name_is_an_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A method has received an invalid argument value for one of its parameters. $fixture must be a string or an array!');

        $fixture = new Fixture($this->configuration);
        $content = $fixture->fixture('');
    }

    /**
     * @test
     * @group Fixture
     */
    public function fixture_throws_an_exception_if_called_with_an_empty_configuration_object(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration object is malformed. Please check it!');

        $fixture = new Fixture();
    }
}
