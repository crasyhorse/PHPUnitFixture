<?php

namespace CrasyHorse\Tests\Feature\Fixture;

use CrasyHorse\Tests\TestCase;
use CrasyHorse\Testing\Fixture;
use CrasyHorse\Testing\Exceptions\InvalidArgumentException;

/**
 * @covers \CrasyHorse\Testing\Fixture
 */
class UnwrapTest extends TestCase
{

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
                    'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'filesystem', 'data']),
                    'default_file_extension' => 'json',
                ],
                'alternative' => [
                    'driver' => 'local',
                    'rootpath' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'filesystem', 'alternative']),
                    'default_file_extension' => 'json',
                ],
            ],
        ];
    }

    public function fixture_provider(): array
    {
        return [
            'a fixture wrapped in "data" element' => [
                'fixture-004.json',
                'data',
                [
                    [
                        'key' => 'FIXTURE-004',
                        'text' => 'Guess what? Yes, a sample text!',
                        'status' => 'sleeping',
                        'updated' => '2021-10-27 10:38:06.0'
                    ]
                ]
            ],
            'a fixture wrapped in "data.metadata" elements' => [
                'fixture-005.json',
                'data.metadata',
                [
                    [
                        'key' => 'FIXTURE-005',
                        'text' => 'Another funny sample text.',
                        'status' => 'sleepy',
                        'updated' => '2021-11-25 14:52:13.0',
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider fixture_provider
     * @testdox Unwrap unwraps the content of a fixture with valid access path "$elementToUnwrap"
     */
    public function unwrap_unwraps_content_from_element(string $fixturefile, string $elementToUnwrap, array $expected): void
    {
        $fixture = new Fixture($this->configuration);

        $fixture->source('alternative')
            ->fixture($fixturefile)
            ->unwrap($elementToUnwrap);

        $actual = $fixture->toArray();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function unwrap_throws_an_exception_if_a_nonexistent_element_should_be_unwrapped(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The element to unwrap must exist and it must be of type array or object.');

        $fixture = new Fixture($this->configuration);

        $fixture->source('alternative')
            ->fixture('fixture-004.json')
            ->unwrap('nonexistingelement')
            ->toArray();
    }

    /**
     * @test
     */
    public function unwrap_throws_an_exception_if_the_value_of_element_to_unwrap_is_not_of_one_of_the_types_array_or_object(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The element to unwrap must exist and it must be of type array or object.');

        $fixture = new Fixture($this->configuration);

        $fixture->source('alternative')
            ->fixture('fixture-004.json')
            ->unwrap('data.0.key')
            ->toArray();
    }

    /**
     * @test
     */
    public function unwrap_returns_the_whole_content_if_executed_with_no_argument_and_data_element_does_not_exist(): void
    {
        $fixture = new Fixture($this->configuration);

        $fixture->source('alternative')
            ->fixture('fixture-006.json')
            ->unwrap();

        $expected = [
            'metadata' => [
                [
                    'key' => 'FIXTURE-004',
                    'text' => 'Guess what? Yes, a sample text!',
                    'status' => 'sleeping',
                    'updated' => '2021-10-27 10:38:06.0',
                ]
            ]
        ];

        $actual = $fixture->toArray();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function unwrap_returns_the_contents_of_the_data_element_even_if_it_is_executed_without_an_argument(): void
    {
        $fixture = new Fixture($this->configuration);

        $fixture->source('alternative')
            ->fixture('fixture-004.json')
            ->unwrap();

        $expected = [
            [
                'key' => 'FIXTURE-004',
                'text' => 'Guess what? Yes, a sample text!',
                'status' => 'sleeping',
                'updated' => '2021-10-27 10:38:06.0',
            ]
        ];

        $actual = $fixture->toArray();
        $this->assertEquals($expected, $actual);
    }
}
