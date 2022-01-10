<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit\Config;

/**
 * Data provider that supplies test data for testing the JSON schema
 * validation of the "loaders.*" property.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
trait LoadersProvider
{
    use CreateConfiguration;
    use ComposeErrorMessage;

    /**
     * Creates test cases with invalid values for the loaders.* property.
     *
     * @return array
     */
    public function invalidLoadersProvider(): array
    {
        if (!defined('LOADERS_ERROR_MESSAGE')) {
            define('LOADERS_ERROR_MESSAGE', 'The properties must match schema: loaders');
        }

        $this->defineErrorConstants();

        $cases = [];

        $config = $this->getconfiguration();
        $config = $this->clearProperty('loaders', $config);

        $cases['is an empty array'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                LOADERS_ERROR_MESSAGE,
                'The data (array) must match the type: object'
            ])
        ];

        $config = $this->createConfiguration('loaders', [
            '' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
        ]);

        $cases['has an empty class alias'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                LOADERS_ERROR_MESSAGE,
                'Additional object properties are not allowed:'
            ])
        ];

        $config = $this->createConfiguration('loaders', [
            'Local' => ''
        ]);

        $cases['has an empty class name'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                LOADERS_ERROR_MESSAGE,
                "Object properties that match pattern '^[A-Za-z0-9]+$' must also match pattern's schema",
                "The string should match pattern: ^\\\\((([A-Z][A-Za-z0-9]+)\\\\)+)[A-Z][A-Za-z0-9]+$"
            ])
        ];

        $config = $this->createConfiguration('loaders', [
            [
                'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
            ]
        ]);

        $cases['is an array instead of an object'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                LOADERS_ERROR_MESSAGE,
                'The data (array) must match the type: object'
            ])
        ];

        return $cases;
    }
}
