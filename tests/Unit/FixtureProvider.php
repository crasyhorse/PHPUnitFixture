<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit;

/**
 * Provides the data provider "fixture_provider" for the
 * "FixtureTest" and "ContentTest" test cases.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
trait FixtureProvider
{
    public function fixture_provider(): array
    {
        return [
            'a single fixture from a json file.' => [
                'fixture-001.json',
                [
                    'data' => [
                        [
                            'key' => 'FIXTURE-001',
                            'text' => 'This is a sample text!',
                            'status' => 'working',
                            'updated' => '2021-10-27 10:35:45.0',
                        ],
                    ],
                ],
                1
            ],
            'a list of two fixtures from json files.' => [
                ['fixture-001.json', 'fixture-002.json'],
                [
                    'data' => [
                        [
                            'key' => 'FIXTURE-001',
                            'text' => 'This is a sample text!',
                            'status' => 'working',
                            'updated' => '2021-10-27 10:35:45.0',
                        ],
                        [
                            'key' => 'FIXTURE-002',
                            'text' => 'This is another sample text!',
                            'status' => 'stopped',
                            'updated' => '2021-10-27 10:36:27.0',
                        ],
                    ],
                ],
                2
            ]
        ];
    }
}
