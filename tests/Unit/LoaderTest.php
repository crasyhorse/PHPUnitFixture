<?php

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Tests\TestCase;
use CrasyHorse\Testing\Config;
use CrasyHorse\Testing\Loader\Loader;
use CrasyHorse\Testing\Loader\File;

class LoaderTest extends TestCase
{
    use Config;
    
    /**
     * The main configuration object.
     *
     * @var array
     */
    protected $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->configuration = [
            'sources' => [
                'default' => [
                    'driver' => 'local',
                    'rootpath' => implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'filesystem', 'data'))
                ],
                'alternative' => [
                    'driver' => 'local',
                    'rootpath' => implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'filesystem', 'alternative'))
                ]
            ]
        ];
    }

    /**
     * @test
     */
    public function loads_a_file_if_an_existing_driver_is_used(): void
    {
        $expected = new File('alternative_fixture.json', '', $this->config('sources.alternative')['rootpath'].'/', 92, 'application/json', 1627230274);
        $content = <<<EOL
{
  "Hello": {
    "content": "This is another simple Json file for testing purposes."
  }
}
EOL;
        $expected->setContent($content);
        
        $actual = Loader::loadFixture('alternative_fixture.json', $this->config('sources.alternative'));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function throws_an_exception_if_an_unknown_loader_is_configured_to_use(): void
    {
        $this->expectException(\CrasyHorse\Testing\Exceptions\LoaderNotFoundException::class);

        $source = $this->config('sources.alternative');
        $source['driver'] = 'nonExistingDriver';

        Loader::loadFixture('alternative_fixture.json', $source);
    }
}
