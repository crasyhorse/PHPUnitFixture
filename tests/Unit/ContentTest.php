<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit;

use CrasyHorse\Tests\TestCase;
use CrasyHorse\Testing\Fixture;
use CrasyHorse\Testing\Exceptions\InvalidArgumentException;
use CrasyHorse\Testing\Config\Config;

/**
 * @covers CrasyHorse\Testing\Content
 * @covers CrasyHorse\Testing\Exceptions\InvalidArgumentException
 */
class ContentTest extends TestCase
{
    use FixtureProvider;

    /**
     * @test
     * @group Content
     * @dataProvider fixture_provider
     * @testdox Executing get with no arguments while loading $_dataName returns the whole contents array
     */
    public function executing_get_with_no_arguments_returns_the_whole_contents_array($fixtures, array $expected): void
    {
        $fixture = new Fixture($this->configuration);
        $content = $fixture->fixture($fixtures);

        $actual = $content->get();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @group Content
     * @dataProvider array_dot_notation_provider
     * @testdox Executing get with the argument "$dotNotation" of type string uses the array dot notation to access content
     */
    public function executing_get_with_one_single_string_argument_uses_array_dot_notation_to_access_content(string $dotNotation, $expected): void
    {
        $fixture = new Fixture($this->configuration);
        $content = $fixture->fixture(['fixture-001.json', 'fixture-002.json']);

        $actual = $content->get($dotNotation);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @group Content
     */
    public function getFromArray_returns_null_if_content_is_empty(): void
    {
        $fixture = new Fixture($this->configuration);
        $content = $fixture->fixture('fixture-004.json');

        $actual = $content->get('data');

        $this->assertNull($actual);
    }

    /**
     * @test
     * @group Content
     */
    public function tojson_returns_the_json_representation_of_the_resolved_fixture_content(): void
    {
        $fixture = new Fixture($this->configuration);
        $content = $fixture->fixture('fixture-001.json');
        $actual = $content->toJson();

        $expected = json_encode([
            'data' => [
                [
                    'key' => 'FIXTURE-001',
                    'text' => 'This is a sample text!',
                    'status' => 'working',
                    'updated' => '2021-10-27 10:35:45.0',
                ],
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @group Content
     */
    public function unwrap_returns_the_complete_content_if_executed_with_no_argument_and_data_element_does_not_exist(): void
    {
        $fixture = new Fixture($this->configuration);

        $actual = $fixture->source('alternative')
            ->fixture('fixture-006.json')
            ->unwrap()
            ->toArray();

        $expected = [
            'metadata' => [
                [
                    'key' => 'FIXTURE-006',
                    'text' => 'Guess what? Yes, a sample text!',
                    'status' => 'sleeping',
                    'updated' => '2021-10-27 10:38:06.0',
                ]
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @group Content
     */
    public function unwrap_returns_the_contents_of_the_data_element_even_if_it_is_executed_without_an_argument(): void
    {
        $fixture = new Fixture($this->configuration);

        $actual = $fixture->source('alternative')
            ->fixture('fixture-004.json')
            ->unwrap()
            ->toArray();

        $expected = [
            [
                'key' => 'FIXTURE-004',
                'text' => 'Guess what? Yes, a sample text!',
                'status' => 'sleeping',
                'updated' => '2021-10-27 10:38:06.0',
            ]
        ];

        $this->assertEquals($expected, $actual);
    }
    /**
     * @test
     * @group Content
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
     * @group Content
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
     * @group Content
     * @dataProvider unwrap_fixture_provider
     * @testdox Unwrap unwraps the content of a fixture with valid access path "$elementToUnwrap"
     */
    public function unwrap_unwraps_content_from_element(string $fixturefile, string $elementToUnwrap, array $expected): void
    {
        $fixture = new Fixture($this->configuration);

        $actual = $fixture->source('alternative')
            ->fixture($fixturefile)
            ->unwrap($elementToUnwrap)
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function array_dot_notation_provider(): array
    {
        return [
            'data' => [
                'data',
                'data' => [
                    [
                        'key' => 'FIXTURE-001',
                        'text' => 'This is a sample text!',
                        'status' => 'working',
                        'updated' => '2021-10-27 10:35:45.0'
                    ],
                    [
                        'key' => 'FIXTURE-002',
                        'text' => 'This is another sample text!',
                        'status' => 'stopped',
                        'updated' => '2021-10-27 10:36:27.0'
                    ]
                ]
            ],
            'data.0' => [
                'data.0',
                [
                    'key' => 'FIXTURE-001',
                    'text' => 'This is a sample text!',
                    'status' => 'working',
                    'updated' => '2021-10-27 10:35:45.0'
                ]
            ],
            'data.1.key' => [
                'data.1.key',
                'FIXTURE-002'
            ],
        ];
    }

    public function unwrap_fixture_provider(): array
    {
        return [
            'a fixture wrapped in "data" element (data is array)' => [
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
            'a fixture wrapped in "data.metadata" element' => [
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
            ],
            'a fixture wrapped in "data" element (data is object)' => [
                'fixture-007.json',
                'data',
                [
                    'key' => 'FIXTURE-007',
                    'text' => 'Oh no, more sample texts!',
                    'status' => 'open',
                    'updated' => '2021-10-27 10:37:14.0',
                ]
            ]
        ];
    }
}
