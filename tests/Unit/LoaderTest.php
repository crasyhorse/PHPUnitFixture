<?php

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Testing\Loader\Loader;
use CrasyHorse\Tests\TestCase;
use League\Flysystem\FileNotFoundException;
use CrasyHorse\Testing\Exceptions\LoaderNotFoundException;

class LoaderTest extends TestCase
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

    public function filename_provider(): array
    {
        return [
            'with the help of an existing driver.' => [
                [
                    'filename' => 'fixture-003.json',
                ]
            ],
            'by using the default file extension if the given fixture does not have one.' => [
                [
                    'filename' => 'fixture-003',
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider filename_provider
     * @testdox Loader loads a fixture $_dataName
     */
    public function loads_a_file(array $data): void
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
        $file = Loader::loadFixture($data['filename'], $this->configuration['sources']['alternative']);
        $actual = $file->getContent();

        $this->assertStringContainsString($expected, $actual);
    }

    /**
     * @test
     * @testdox Loader loads an empty fixture
     */
    public function loads_an_empty_fixture_file(): void
    {
        $file = Loader::loadFixture('fixture-004', $this->configuration['sources']['default']);
        
        $this->assertEquals('', $file->getContent());
        $this->assertEquals(0.0, $file->getSize());
        $this->assertEquals('application/json', $file->getMimeType());
        $this->assertEquals('fixture-004', $file->getFilename());
        $this->assertEquals('json', $file->getExtension());
        $this->assertEquals('/home/fweidinger/workspace/phpunitFixture/tests/Unit/../filesystem/data/', $file->getPath());
    }

    /**
     * @test
     * @testdox Initializing the Loader throws an exception if an unknown Loader is passed
     */
    public function throws_an_exception_if_an_unknown_loader_is_configured_to_use(): void
    {
        $this->expectException(LoaderNotFoundException::class);

        $source = $this->configuration['sources']['alternative'];
        $source['driver'] = 'nonExistingDriver';

        Loader::loadFixture('fixture-003.json', $source);
    }

    /**
     * @test
     * @testdox Initializing the Loader throws an exception if a source could not be used for loading a fixture
     */
    public function throws_an_exception_if_a_source_could_not_be_used_for_loading(): void
    {
        $this->expectException(FileNotFoundException::class);

        $source = $this->configuration['sources']['default'];

        Loader::loadFixture('http://www.fileserver.de/fixture-003.json', $source);
    }
}
