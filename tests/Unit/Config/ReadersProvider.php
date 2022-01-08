<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit\Config;

/**
 * Data provider that supplies test data for testing the JSON schema
 * validation of the "readers.*" property.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
trait ReadersProvider
{
    use CreateConfiguration;
    use ComposeErrorMessage;

    /**
     * Creates test cases with invalid values for the readers.* property.
     *
     * @return array
     */
    public function invalidReadersProvider(): array
    {
        if (!defined('READERS_ERROR_MESSAGE')) {
            define('READERS_ERROR_MESSAGE', 'The properties must match schema: readers');
        }

        $this->defineErrorConstants();

        $cases = [];

        $config = $this->getconfiguration();
        $config = $this->clearProperty('readers', $config);

        $cases['is an empty array'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                READERS_ERROR_MESSAGE,
                'The data (array) must match the type: object'
            ])
        ];

        $config = $this->createConfiguration('readers', [
            '' => '\\CrasyHorse\\Testing\\Reader\\JsonReader'
        ]);

        $cases['has an empty class alias'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                READERS_ERROR_MESSAGE,
                'Additional object properties are not allowed:'
            ])
        ];

        $config = $this->createConfiguration('readers', [
            'application/json' => ''
        ]);

        $cases['has an empty class name'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                READERS_ERROR_MESSAGE,
                "Object properties that match pattern '^[A-Za-z0-9\\/\\*]+$' must also match pattern's schema",
                "The string should match pattern: ^\\\\((([A-Z][A-Za-z0-9]+)\\\\)+)[A-Z][A-Za-z0-9]+$"
            ])
        ];

        $config = $this->createConfiguration('readers', [
            [
                'application/json' => '\\CrasyHorse\\Testing\\Reader\\JsonReader'
            ]
        ]);

        $cases['is an array instead of an object'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                READERS_ERROR_MESSAGE,
                'The data (array) must match the type: object'
            ])
        ];

        return $cases;
    }
}
