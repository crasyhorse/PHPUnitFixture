<?php

namespace CrasyHorse\Tests\Feature;

use CrasyHorse\Testing\Reader\Reader;
use CrasyHorse\Tests\TestCase;

/**
 * @covers \CrasyHorse\Testing\Reader\AbstractReader
 * @covers \CrasyHorse\Testing\Reader\JsonReader
 * @covers \CrasyHorse\Testing\Reader\Reader
 */
class ReaderTest extends TestCase
{
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
     * @group Reader
     */
    public function read_returns_the_processed_file_contents_as_array(): void
    {
        $expected = [
            "data" => [
                [
                    "key" => "FIXTURE-004",
                    "text" => "Guess what? Yes, a sample text!",
                    "status" => "sleeping",
                    "updated" => "2021-10-27 10:38:06.0"
                ]
            ]
        ];

        $actual = Reader::read('fixture-004.json', $this->configuration['sources']['alternative']);

        $this->assertEquals($expected, $actual);
    }

    public function fixture_data_provider(): array
    {
        return [
            'there is no reader for the given data' => [
                'filename' => 'fixture-998.csv',
                'source' => 'alternative'
            ],
            'there is a reader but the file is empty' => [
                'filename' => 'fixture-004.json',
                'source' => 'default'
            ]
        ];
    }
    /**
     * @test
     * @group Reader
     * @dataProvider fixture_data_provider
     * @testdox Read returns an empty array if $_dataName
     */
    public function read_returns_an_empty_array(string $filename, string $source): void
    {
        $fixtureWithUnknownMimeType = $filename;
        $actual = Reader::read($fixtureWithUnknownMimeType, $this->configuration['sources'][$source]);

        $this->assertEmpty($actual);
    }
}
