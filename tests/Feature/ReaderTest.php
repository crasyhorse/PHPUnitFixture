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
        $temp = <<<EOL
{
    "data": [
        {
            "key": "FIXTURE-003",
            "text": "Once again a sample text!",
            "status": "open",
            "updated": "2021-10-27 10:37:14.0"
        }
    ]
}
EOL;

        $expected = preg_replace('~\R~u', "\r\n", $temp);

        $actual = Reader::read('fixture-003.json', $this->config('sources.alternative'));

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
