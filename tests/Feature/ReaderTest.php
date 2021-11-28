<?php

namespace CrasyHorse\Tests\Feature;

use CrasyHorse\Testing\Config;
use CrasyHorse\Testing\Reader\Reader;
use CrasyHorse\Tests\TestCase;

class ReaderTest extends TestCase
{
    use Config;

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

        $actual = Reader::read('fixture-004.json', $this->config('sources.alternative'));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @group Reader
     */
    public function read_returns_null_if_there_is_no_reader_for_the_given_mime_type(): void
    {
        $fixtureWithUnknownMimeType = 'fixture-999.csv';
        $actual = Reader::read($fixtureWithUnknownMimeType, $this->config('sources.alternative'));

        $this->assertNull($actual);
    }
}
