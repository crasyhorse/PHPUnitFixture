<?php

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Testing\Loader\LocalLoader;
use CrasyHorse\Tests\TestCase;

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

        $this->loader = new LocalLoader();
    }

    /**
     * @test
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

        $file = $this->loader->load('fixture-003.json', $this->configuration['sources']['alternative']);
        $actual = $file->getContent();
        $this->assertStringContainsString($expected, $actual);
    }

    /**
     * @test
     */
    public function load_throws_an_exception_if_the_fixture_to_load_is_missing(): void
    {
        $this->expectException(\League\Flysystem\FileNotFoundException::class);

        $nonexistingFixtureFilename = 'fixture-999.json';
        $this->loader->load($nonexistingFixtureFilename, $this->configuration['sources']['alternative']);
    }
}
