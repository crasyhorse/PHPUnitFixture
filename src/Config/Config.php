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
     * @var \CrasyHorse\Testing\Config\Config
     */
    private static $instance;

    /**
     * @var array $configuration
     */
    protected $configuration;

    private function __construct(array $configuration = [])
    {
        $this->configuration = $this->validate($configuration);
    }

    /**
     * Creates a new instance of the Config object (singleton).
     *
     * @param array $configuration The configuration object to be used for initialization.
     *
     * @return \CrasyHorse\Testing\Config\Config
     */
    public static function getInstance(array $configuration = []): self
    {
        if (!self::$instance) {
            self::$instance = new self($configuration);
        }

        return self::$instance;
    }

    /**
     * Re-initializes the Config object.
     *
     * @param array $configuration The configuration object to be used for initialization.
     *
     * @return void
     */
    public static function reInitialize(array $configuration): void
    {
        self::$instance = new self($configuration);
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
        $validator->resolver()
            ->registerFile(
                'https://github.com/crasyhorse/PHPUnitFixture/configSchema.json',
                __DIR__ . DIRECTORY_SEPARATOR . 'configSchema.json'
            );

        $data = Helper::toJSON($configuration);
        $result = $validator->validate(
            $data,
            'https://github.com/crasyhorse/PHPUnitFixture/configSchema.json'
        );

        if (!$result->isValid()) {
            $formatter = new ErrorFormatter();
            $validationErrors = $formatter->formatFlat($result->error());
            throw new InvalidConfigurationException($validationErrors);
        }

        return $configuration;
    }

    /**
     * @codeCoverageIgnore
     */
    private function __clone()
    {
        // Empty method implementation to ensure usage of Singleton pattern.
    }

    /**
     * @codeCoverageIgnore
     */
    private function __wakeup()
    {
        // Empty method implementation to ensure usage of Singleton pattern.
    }
}