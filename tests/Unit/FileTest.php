<?php

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Testing\Loader\Loader;
use CrasyHorse\Tests\TestCase;
use Carbon\Carbon;

class FileTest extends TestCase
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
     */
    public function getter_return_values(): void
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

        $expectedContent = preg_replace('~\R~u', "\r\n", $temp);
        $actual = Loader::loadFixture('fixture-003.json', $this->configuration['sources']['alternative']);

        $this->assertEquals($expectedContent, $actual->getContent());
        $this->assertEquals(212.0, $actual->getSize());
        $this->assertEquals('application/json', $actual->getMimeType());
        $this->assertEquals('fixture-003', $actual->getFilename());
        $this->assertEquals('json', $actual->getExtension());
        $this->assertEquals('/home/fweidinger/workspace/phpunitFixture/tests/Unit/../filesystem/alternative/', $actual->getPath());
        $this->assertEquals(new Carbon('2021-11-28 16:11:33.000000'), $actual->getTimestamp());
    }

    public function setters_set_correct_values(): void
    {
        # code...
    }
}
