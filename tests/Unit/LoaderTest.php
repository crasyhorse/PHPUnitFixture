<?php

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Testing\Config;
use CrasyHorse\Testing\Loader\File;
use CrasyHorse\Testing\Loader\Loader;
use CrasyHorse\Tests\TestCase;

class LoaderTest extends TestCase
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
            'the help of an existing driver' => [
                [
                    'filename' => 'fixture-003.json',
                ]
            ],
            'the default file extension if the given fixture does not have one' => [
                [
                    'filename' => 'fixture-003',
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider filename_provider
     */
    public function loads_a_file(array $data): void
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
        $actual = Loader::loadFixture($data['filename'], $this->config('sources.alternative'));

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

        Loader::loadFixture('fixture-003.json', $source);
    }
}
