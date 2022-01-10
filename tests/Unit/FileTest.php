<?php

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Testing\Loader\Loader;
use CrasyHorse\Tests\TestCase;
use Carbon\Carbon;
use CrasyHorse\Testing\Config\Config;

/**
 * @covers CrasyHorse\Testing\Loader\File
 */
class FileTest extends TestCase
{
    /**
     * @test
     * @group File
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

        Config::reInitialize($this->configuration);
        $expectedContent = preg_replace('~\R~u', "\r\n", $temp);
        $actual = Loader::loadFixture('fixture-003.json', 'alternative');

        $this->assertEquals($expectedContent, $actual->getContent());
        $this->assertEquals(212.0, $actual->getSize());
        $this->assertEquals('application/json', $actual->getMimeType());
        $this->assertEquals('fixture-003', $actual->getFilename());
        $this->assertEquals('json', $actual->getExtension());
        $this->assertEquals('/home/fweidinger/workspace/phpunitFixture/tests/data/alternative/', $actual->getPath());
    }

    public function setters_set_correct_values(): void
    {
        # code...
    }
}
