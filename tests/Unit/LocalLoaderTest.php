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
     * The main configuration object.
     *
     * @var array
     */
    protected $config;

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
            'alternative_fixture.json',
            '',
            $this->config('sources.alternative')['rootpath'].'\\',
            92,
            'application/json',
            1635316895
        );

        $content = <<<EOL
{
  "Hello": {
    "content": "This is another simple Json file for testing purposes."
  }
}
EOL;
        $expected->setContent($content);

        $this->loader = new LocalLoader();

        $actual = $this->loader->load('alternative_fixture.json', $this->config('sources.alternative'));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function load_throws_an_exception_if_the_file_to_load_is_missing(): void
    {
        $this->expectException(\League\Flysystem\FileNotFoundException::class);

        $actual = $this->loader->load('missing_fixture.json', $this->config('sources.alternative'));
    }
}
