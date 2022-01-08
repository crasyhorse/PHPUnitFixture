<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit\Config;

/**
 * Data provider for testing the source method of the Config class.
 *
 * @author Florian Weidinger
 * @since  0.3.0
 */
trait SourceMethodProvider
{
    /**
     * Creates test cases for the Config::source method.
     *
     * @return array
     */
    public function sourceMethodProvider(): array
    {
        return [
            'returns the "driver" attribute from the alternative source object.' => [
                'source' => 'alternative',
                'attribute' => 'driver',
                'value' => 'Local'
            ],
            'returns the "default_file_extension" attribute from the default source (source left empty).' => [
                'source' => '',
                'attribute' => 'default_file_extension',
                'value' => 'json'
            ],
            'returns the complete default source object.' => [
                'source' => 'default',
                'attribute' => '',
                'value' => [
                    'driver' => 'Local',
                    'root_path' => '/home/fweidinger/workspace/phpunitFixture/tests/data/default',
                    'default_file_extension' => 'json',
                ]
            ],
            'returns the complete default source object (source left empty).' => [
                'source' => '',
                'attribute' => '',
                'value' => [
                    'driver' => 'Local',
                    'root_path' => '/home/fweidinger/workspace/phpunitFixture/tests/data/default',
                    'default_file_extension' => 'json',
                ]
            ]
        ];
    }
}
