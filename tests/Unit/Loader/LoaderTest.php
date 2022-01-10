<?php

namespace CrasyHorse\Tests\Unit\Loader;

use CrasyHorse\Testing\Loader\Loader;
use CrasyHorse\Tests\TestCase;
use League\Flysystem\FileNotFoundException;
use CrasyHorse\Testing\Config\Config;
use CrasyHorse\Testing\Exceptions\LoaderNotFoundException;

/**
 * @covers CrasyHorse\Testing\Loader\Loader
 * @covers CrasyHorse\Testing\Loader\AbstractLoader
 * @covers CrasyHorse\Testing\Loader\LocalLoader
 * @covers CrasyHorse\Testing\Exceptions\LoaderNotFoundException
 */
class LoaderTest extends TestCase
{
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
     * @group Loader
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
        Config::reInitialize($this->configuration);
        $file = Loader::loadFixture($data['filename'], 'alternative');
        $actual = $file->getContent();

        $this->assertStringContainsString($expected, $actual);
    }

    /**
     * @test
     * @group Loader
     * @testdox Loader loads an empty fixture
     */
    public function loads_an_empty_fixture_file(): void
    {
        Config::reInitialize($this->configuration);
        $file = Loader::loadFixture('fixture-004', 'default');

        $this->assertEquals('', $file->getContent());
        $this->assertEquals(0.0, $file->getSize());
        $this->assertEquals('application/json', $file->getMimeType());
        $this->assertEquals('fixture-004', $file->getFilename());
        $this->assertEquals('json', $file->getExtension());
        $this->assertEquals('/home/fweidinger/workspace/phpunitFixture/tests/data/default/', $file->getPath());
    }

    /**
     * @test
     * @group Loader
     * @testdox InstantiateLoader throws an exception it $_dataName
     */
    public function instantiateLoader_throws_an_exception_if_an_unknown_loader_class_is_configured(): void
    {
        $this->expectException(LoaderNotFoundException::class);
        $this->expectExceptionMessage('Could not find a loader for the Local driver.');

        $configuration = [
            'readers' => [
                'Local' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
                'Binary' => '\\CrasyHorse\\Testing\\Reader\BinaryReader'
            ],
            'encoders' => [
                'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
            ],
            'loaders' => [
                'Local' => '\\CrasyHorse\\Testing\\Loader\\Local'
            ],
            'sources' => [
                'default' => [
                    'driver' => 'Local',
                    'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
                    'default_file_extension' => 'json',
                ],
            ],
        ];

        Config::reInitialize($configuration);
        $file = Loader::loadFixture('fixture-004', 'default');
    }
    /**
     * @test
     * @group Loader
     * @testdox Initializing the Loader throws an exception if a source could not be used for loading a fixture
     */
    public function loadFixture_throws_an_exception_if_an_unknown_fixture_should_be_loaded(): void
    {
        $this->expectException(FileNotFoundException::class);

        Config::reInitialize($this->configuration);
        $file = Loader::loadFixture('fixture-666', 'default');
    }

    /**
     * @test
     * @group Loader
     * @testdox Initializing the Loader throws an exception if a source could not be used for loading a fixture
     */
    public function loadFixture_throws_an_exception_if_a_source_could_not_be_used_for_loading(): void
    {
        $this->expectException(FileNotFoundException::class);

        Config::reInitialize($this->configuration);
        Loader::loadFixture('http://www.fileserver.de/fixture-003.json', 'default');
    }
}
