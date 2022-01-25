<?php

declare(strict_types=1);

namespace CrasyHorse\Testing\Config;

use Adbar\Dot;
use Opis\JsonSchema\Validator;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Helper;
use CrasyHorse\Testing\Exceptions\InvalidConfigurationException;

/**
 * This is the configuration object. It includes a json-schema for validation purposes.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
class Config
{
    /**
     * @var array $configuration
     */
    protected $configuration;

    public function __construct(array $configuration = [])
    {
        $this->configuration = $this->validate($configuration);
    }

    /**
     * Wrapper for Dot::get to return the configuration.
     *
     * @param string|null $name The name of the configuration object to return. If
     *    no name is given the complete configuration object will be returned.
     *
     * @return array|string|null
     */
    public function get(string $name = null)
    {
        $dot = new Dot($this->configuration);

        /** @var array|string|null */
        return $dot->get($name);
    }

    /**
     * Helper method that makes it easier to access attributes of a source's
     * configuration. If $source is left empty the method tries to access
     * the default source.
     *
     * @param string $dotNotation Array dot notation to access an attribute
     * @param string $source The source to access
     *
     * @return array|string|null
     */
    public function source(string $dotNotation = null, string $source = null)
    {
        if (empty($source)) {
            $source = 'default';
        }

        if (empty($dotNotation)) {
            return $this->get("sources.{$source}");
        }

        return $this->get("sources.{$source}.{$dotNotation}");
    }

    /**
     * Validates the configuration object.
     *
     * @param array $configuration
     *
     * @return array
     * @throws \CrasyHorse\Testing\Exceptions\InvalidConfigurationException
     */
    private function validate(array $configuration): array
    {
        $validator = new Validator();
        $resolver = $validator->resolver();

        /** @var \Opis\JsonSchema\Resolvers\SchemaResolver $resolver */
        $resolver->registerFile(
            'https://github.com/crasyhorse/PHPUnitFixture/configSchema.json',
            __DIR__ . DIRECTORY_SEPARATOR . 'configSchema.json'
        );

        /** @var object $data */
        $data = Helper::toJSON($configuration);
        $result = $validator->validate(
            $data,
            'https://github.com/crasyhorse/PHPUnitFixture/configSchema.json'
        );

        if (!$result->isValid()) {
            $formatter = new ErrorFormatter();

            /** @var \Opis\JsonSchema\Errors\ValidationError $validationErrors */
            $validationErrors = $result->error();
            $formattedValidationErrors = $formatter->formatFlat($validationErrors);

            throw new InvalidConfigurationException($formattedValidationErrors);
        }

        return $configuration;
    }
}
