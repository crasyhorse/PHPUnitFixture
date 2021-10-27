<?php

namespace CrasyHorse\Tests\Unit;

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
                ],
                'alternative' => [
                    'driver' => 'local',
                    'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'filesystem', 'alternative']),
                ],
            ],
        ];
    }

    /**
     * @test
     * @group Reader
     */
    public function read_returns_the_processed_file_contents_as_json_string(): void
    {
        $expected = <<<EOL
{
  "Hello": {
    "content": "This is another simple Json file for testing purposes."
  }
}
EOL;

        $actual = Reader::read('alternative_fixture.json', $this->config('sources.alternative'));

        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     * @group Reader
     */
    public function read_returns_null_if_there_is_no_reader_for_the_given_mime_type(): void
    {
        $actual = Reader::read('alternative_csv_fixture.csv', $this->config('sources.alternative'));

        $this->assertNull($actual);
    }
}
