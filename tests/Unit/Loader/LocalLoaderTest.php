<?php

namespace CrasyHorse\Tests\Unit\Loader;

use CrasyHorse\Testing\Loader\LocalLoader;
use CrasyHorse\Tests\TestCase;
use CrasyHorse\Testing\Config\Config;

/**
 * @covers CrasyHorse\Testing\Loader\LocalLoader
 */
class LocalLoaderTest extends TestCase
{
    /**
     * The loader to use to load files.
     *
     * @var CrasyHorse\Testing\Loader\LocalLoader
     */
    protected $loader;

    public function setUp(): void
    {
        parent::setUp();

        $this->loader = new LocalLoader();
    }

    /**
     * @test
     * @group Loader
     */
    public function load_can_load_an_existing_fixture_from_local_filesystem(): void
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
        $this->loader = new LocalLoader();

        $config = new Config($this->configuration);
        $file = $this->loader->load('fixture-003.json', 'alternative', $config);
        $actual = $file->getContent();
        $this->assertStringContainsString($expected, $actual);
    }

    /**
     * @test
     * @group Loader
     */
    public function load_throws_an_exception_if_the_fixture_to_load_is_missing(): void
    {
        $this->expectException(\League\Flysystem\FileNotFoundException::class);

        $config = new Config($this->configuration);
        $nonexistingFixtureFilename = 'fixture-999.json';
        $this->loader->load($nonexistingFixtureFilename, 'alternative', $config);
    }
}
