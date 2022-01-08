<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit\Config;

/**
 * Data provider that supplies test data for testing the JSON schema
 * validation of the "encoders.*" property.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
trait EncodersProvider
{
    use CreateConfiguration;
    use ComposeErrorMessage;

    /**
     * Creates test cases with invalid values for the encoders.* property.
     *
     * @return array
     */
    public function invalidEncodersProvider(): array
    {
        if (!defined('ENCODERS_ERROR_MESSAGE')) {
            define('ENCODERS_ERROR_MESSAGE', 'The properties must match schema: encoders');
        }

        $this->defineErrorConstants();

        $cases = [];

        $config = $this->getconfiguration();
        $config = $this->clearProperty('encoders', $config);

        $cases['is an empty array'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                ENCODERS_ERROR_MESSAGE,
                'The data (array) must match the type: object'
            ])
        ];

        $config = $this->createConfiguration('encoders', [
            '' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
        ]);

        $cases['has an empty class alias'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                ENCODERS_ERROR_MESSAGE,
                'Additional object properties are not allowed:'
            ])
        ];

        $config = $this->createConfiguration('encoders', [
            'base64' => ''
        ]);

        $cases['has an empty class name'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                ENCODERS_ERROR_MESSAGE,
                "Object properties that match pattern '^[A-Za-z0-9]+$' must also match pattern's schema",
                "The string should match pattern: ^\\\\((([A-Z][A-Za-z0-9]+)\\\\)+)[A-Z][A-Za-z0-9]+$"
            ])
        ];

        $config = $this->createConfiguration('encoders', [
            [
                'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
            ]
        ]);

        $cases['is an array instead of an object'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                ENCODERS_ERROR_MESSAGE,
                'The data (array) must match the type: object'
            ])
        ];

        return $cases;
    }
}
