<?php

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Testing\Config;
use CrasyHorse\Testing\Loader\File;
use CrasyHorse\Testing\Loader\LocalLoader;
use CrasyHorse\Tests\TestCase;

class LocalLoaderTest extends TestCase
{
    use Config;

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
    public function load_can_load_an_existing_file_from_local_filesystem(): void
    {
        $expected = new File(
            'fixture-003.json',
            '',
            $this->config('sources.alternative')['rootpath'].'/',
            212.0,
            'application/json',
            1637516068
        );

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

        $content = preg_replace('~\R~u', "\r\n", $temp);
        $expected->setContent($content);

        $this->loader = new LocalLoader();

        $actual = $this->loader->load('fixture-003.json', $this->config('sources.alternative'));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function load_throws_an_exception_if_the_file_to_load_is_missing(): void
    {
        $this->expectException(\League\Flysystem\FileNotFoundException::class);

        $nonexistingFixtureFilename = 'fixture-999.json';
        $actual = $this->loader->load($nonexistingFixtureFilename, $this->config('sources.alternative'));
    }
}
