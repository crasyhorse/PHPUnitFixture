<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit\Config;

use CrasyHorse\Tests\TestCase;
use CrasyHorse\Testing\Config\Config;
use CrasyHorse\Testing\Exceptions\InvalidConfigurationException;

class ConfigTest extends TestCase
{
    use EncodersProvider;
    use LoadersProvider;
    use ReadersProvider;
    use SourcesPropertyProvider;
    use SourceMethodProvider;

    /**
     * @test
     * @group Config
     * @testdox A configuration object where the loaders property $_dataName fails schema validation
     * @dataProvider invalidEncodersProvider
     */
    public function a_configuration_object_where_the_encoders_property_is_invalid_fails_schema_validation(array $configurationObject, string $message): void
    {
        $this->invalidConfigurationObject($configurationObject, $message, InvalidConfigurationException::class);
    }

    /**
     * @test
     * @group Config
     * @testdox A configuration object where the loaders property $_dataName fails schema validation
     * @dataProvider invalidLoadersProvider
     * @coversNothing
     */
    public function a_configuration_object_where_the_loaders_property_is_invalid_fails_schema_validation(array $configurationObject, string $message): void
    {
        $this->invalidConfigurationObject($configurationObject, $message, InvalidConfigurationException::class);
    }

    /**
     * @test
     * @group Config
     * @testdox A configuration object where the readers property $_dataName fails schema validation
     * @dataProvider invalidReadersProvider
     * @coversNothing
     */
    public function a_configuration_object_where_the_readers_property_is_invalid_fails_schema_validation(array $configurationObject, string $message): void
    {
        $this->invalidConfigurationObject($configurationObject, $message, InvalidConfigurationException::class);
    }
    /**
     * @test
     * @group Config
     * @testdox A configuration object where $_dataName
     * @dataProvider invalidSourcesPropertyProvider
     * @coversNothing
     */
    public function a_configuration_object_where_the_sources_property_is_invalid_fails_schema_validation(array $configurationObject, string $message): void
    {
        $this->invalidConfigurationObject($configurationObject, $message, InvalidConfigurationException::class);
    }

    /**
     * @test
     * @group Config
     * @coversNothing
     */
    public function a_configuration_object_with_a_valid_encoders_property_passes_schema_validation(): void
    {
        $configurationObject = $this->createConfiguration('encoders', [
            'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
        ]);

        $this->validConfigurationObjectTest($configurationObject);
    }

    /**
     * @test
     * @group Config
     * @testdox A configuration object where the sources.*.root_path property has a $_dataName passes schema valildation.
     * @dataProvider validRootPathPropertyProvider
     * @coversNothing
     */
    public function a_configuration_object_with_a_valid_root_path_property_passes_schema_validation(array $configurationObject): void
    {
        $this->validConfigurationObjectTest($configurationObject);
    }

    /**
     * @test
     * @group Config
     * @testdox A configuration object with a valid sources property with $_dataName passes schema valildation.
     * @dataProvider validSourcesPropertyProvider
     * @coversNothing
     */
    public function a_configuration_object_with_a_valid_sources_property_passes_schema_validation(array $configurationObject): void
    {
        $this->validConfigurationObjectTest($configurationObject);
    }

    /**
       * @test
       * @group Config
       * @testdox Executing get with no arguments returns the whole configuration as an array
       */
    public function executing_get_with_no_arguments_returns_the_whole_configuration(): void
    {
        $this->assertEquals($this->configuration, (new Config($this->configuration))->get());
    }

    /**
     * @test
     * @group Config
     * @testdox The source method $_dataName
     * @dataProvider sourceMethodProvider
     */
    public function source_returns_an_attribute_of_the_given_source_object(string $source, string $attribute, $expected): void
    {
        $config = new Config($this->configuration);

        $actual = $config->source($attribute, $source);

        $this->assertEquals($expected, $actual);
    }

    private function invalidConfigurationObject(array $configurationObject, string $message, string $exception): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);

        $config = new Config($configurationObject);
    }

    private function validConfigurationObjectTest(array $configurationObject): void
    {
        $config = new Config($configurationObject);

        $this->assertEquals($configurationObject, $config->get());
    }
}
