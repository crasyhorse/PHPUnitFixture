<?php

namespace CrasyHorse\Tests\Unit\Reader;

use CrasyHorse\Testing\Reader\Reader;
use CrasyHorse\Tests\TestCase;
use CrasyHorse\Testing\Config\Config;
use CrasyHorse\Testing\Exceptions\InvalidEncodingException;
use CrasyHorse\Testing\Exceptions\ReaderNotFoundException;
use CrasyHorse\Tests\Unit\Config\CreateConfiguration;
use CrasyHorse\Testing\Exceptions\NoSuitableReaderFoundException;
use CrasyHorse\Testing\Exceptions\SourceNotFoundException;

/**
 * @covers CrasyHorse\Testing\Reader\Reader
 * @covers CrasyHorse\Testing\Reader\AbstractReader
 * @covers CrasyHorse\Testing\Reader\BinaryReader
 * @covers CrasyHorse\Testing\Reader\JsonReader
 * @covers CrasyHorse\Testing\Exceptions\InvalidEncodingException
 * @covers CrasyHorse\Testing\Exceptions\ReaderNotFoundException
 * @covers CrasyHorse\Testing\Exceptions\NoSuitableReaderFoundException
 */
class ReaderTest extends TestCase
{
    use CreateConfiguration;

    /**
     * @test
     * @group Reader
     */
    public function abstract_reader_throws_an_exception_if_a_non_existing_source_object_is_given(): void
    {
        $this->expectException(SourceNotFoundException::class);
        $this->expectExceptionMessage("The selected source 'alternative' could not be found. Please configure it or use the default source.");

        $configuration = $this->getConfiguration();
        Config::reInitialize($configuration);
        Reader::read('fixture-998.csv', 'alternative');
    }

    /**
     * @test
     * @group Reader
     */
    public function initEncoder_throws_InvalidEncodingException_if_an_unknown_encoder_should_be_used(): void
    {
        $this->expectException(InvalidEncodingException::class);
        $this->expectExceptionMessage("Invalid encoding. Either the encoder ubjson could not be found in the encoders-list or the selected encoding is not valid for the given mime-type.");

        $configuration = $this->createConfiguration('sources.alternative', [
            'driver' => 'Local',
            'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'data', 'alternative']),
            'default_file_extension' => 'json',
            'encode' => [
                [
                    'mime-type' => '*/*',
                    'encoder' => 'ubjson'
                ]
            ]
        ]);

        Config::reInitialize($configuration);
        Reader::read('fixture-998.csv', 'alternative');
    }

    /**
     * @test
     * @group Reader
     */
    public function instantiateReader_throws_an_exception_if_the_given_reader_could_not_be_instantiated(): void
    {
        $this->expectException(ReaderNotFoundException::class);
        $this->expectExceptionMessage("Could not find a reader for the application/json alias.");

        $configuration = $this->createConfiguration('readers', [
            'application/json' => '\\CrasyHorse\\Testing\\JsonReader'
        ]);

        Config::reInitialize($configuration);
        Reader::read('fixture-001.json', 'default');
    }

    /**
     * @test
     * @group Reader
     * @textdox Read returns the processed contents of a $_dataName as array
     * @dataProvider fixture_provider
     */
    public function read_returns_the_processed_file_contents_as_array(string $filename, array $expected): void
    {
        $configuration = $this->createConfiguration('encoders', [
            'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
        ]);

        $configuration = $this->addProperty(
            'sources.alternative',
            [
                'driver' => 'Local',
                'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'data', 'alternative']),
                'default_file_extension' => 'json',
            ],
            $configuration
        );

        Config::reInitialize($configuration);
        $actual = Reader::read($filename, 'alternative');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @group Reader
     */
    public function read_returns_encoded_content_if_encoding_is_set(): void
    {
        $configuration = $this->createConfiguration('encoders', [
            'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
        ]);

        $configuration = $this->addProperty(
            'sources.alternative',
            [
                'driver' => 'Local',
                'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'data', 'alternative']),
                'default_file_extension' => 'json',
                'encode' => [
                    [
                        'mime-type' => '*/*',
                        'encoder' => 'base64'
                    ]
                ]
            ],
            $configuration
        );

        Config::reInitialize($configuration);
        $expected = ['RklYVFVSRS0wMDMsT25jZSBhZ2FpbiBhIHNhbXBsZSB0ZXh0ISxvcGVuLDIwMjEtMTAtMjcgMTA6Mzc6MTQuMApGSVhUVVJFLTAwNCxHdWVzcyB3aGF0PyBZZXMsIGEgc2FtcGxlIHRleHQhLHNsZWVwaW5nLDIwMjEtMTAtMjcgMTA6Mzg6MDYuMAo='];
        $actual = Reader::read('fixture-998.csv', 'alternative');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @group Reader
     */
    public function read_throws_an_exception_if_no_suitable_reader_could_be_found_for_processing_a_fixture(): void
    {
        $this->expectException(NoSuitableReaderFoundException::class);
        $this->expectExceptionMessage("No suitable reader found to read fixture fixture-998.csv");

        $configuration = $this->createConfiguration('sources.alternative', [
            'driver' => 'Local',
            'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'data', 'alternative']),
            'default_file_extension' => 'json',
        ]);

        $configuration = $this->deleteProperty('readers.*/*', $configuration);
        $configuration = $this->deleteProperty('source.default.encode', $configuration);

        Config::reInitialize($configuration);
        Reader::read('fixture-998.csv', 'alternative');
    }

    public function fixture_provider(): array
    {
        return [
            'JSON file' => [
                'filename' => 'fixture-004.json',
                'expected' => [
                    "data" => [
                        [
                            "key" => "FIXTURE-004",
                            "text" => "Guess what? Yes, a sample text!",
                            "status" => "sleeping",
                            "updated" => "2021-10-27 10:38:06.0"
                        ]
                    ]
                ]
            ],
            'csv file' => [
                'filename' => 'fixture-998.csv',
                'expected' => ["FIXTURE-003,Once again a sample text!,open,2021-10-27 10:37:14.0\nFIXTURE-004,Guess what? Yes, a sample text!,sleeping,2021-10-27 10:38:06.0\n"]
            ]
        ];
    }
}
