<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit\Config;

/**
 * Data provider that supplies test data for testing the JSON schema
 * validation of the "sources.*" property.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
trait SourcesPropertyProvider
{
    use CreateConfiguration;
    use ComposeErrorMessage;

    /**
     * Creates test cases with invalid values for the sources.* property.
     *
     * @return array
     */
    public function invalidSourcesPropertyProvider(): array
    {
        if (!defined('SOURCES_ERROR_MESSAGE')) {
            define('SOURCES_ERROR_MESSAGE', 'The properties must match schema: sources');
        }
        $this->defineErrorConstants();

        $cases = [];

        $config = $this->getConfiguration();

        $config = $this->deleteProperty('sources', $config);

        $cases['the "sources" property is missing fails schema validation.'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                'The required properties (sources) are missing'
            ])
        ];

        $config = $this->createConfiguration('sources.alternative', [
                'driver' => 'Local',
                'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'data', 'default']),
                'default_file_extension' => 'json',
        ]);
        $config = $this->deleteProperty('sources.default', $config);

        $cases['the default configuration is missing fails schema validation.'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                'The required properties (default) are missing'
            ])
        ];

        $config = $this->createConfiguration('sources.default', [
            'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'data', 'default']),
            'default_file_extension' => 'json',
        ]);

        $cases['the sources.default.driver property is missing fails schema validation.'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE,
                'The required properties (driver) are missing'
            ])
        ];

        $config = $this->createConfiguration('sources.default', [
            'driver' => 'remote',
            'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'data', 'default']),
            'default_file_extension' => 'json',
        ]);

        $cases['the value sources.default.driver property is invalid fails schema validation.'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE,
                'The properties must match schema: driver',
                'The data should match one item from enum',
                ])
        ];

        $config = $this->createConfiguration('sources.default', [
            'driver' => 'Local',
            'default_file_extension' => 'json',
        ]);

        $cases['the sources.default.root_path property is missing fails schema validation.'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE,
                'The required properties (root_path) are missing'
            ])
        ];

        $config = $this->createConfiguration('sources.default', [
            'driver' => 'Local',
            'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'data', 'default']),
            'default_file_extension' => 1,
        ]);

        $cases['the sources.default.default_file_extension property is invalid fails schema validation.'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE,
                'The properties must match schema: default_file_extension',
                'The data (integer) must match the type: string',
            ])
        ];

        $cases = array_merge($cases, $this->invalidEncodeProperty());

        return $cases;
    }

    /**
     * Creates test cases with valid values for the sources.* property.
     *
     * @return array
     */
    public function validSourcesPropertyProvider(): array
    {
        $cases = [];

        $config = $this->getConfiguration();
        $config = $this->deleteProperty('sources.default.default_file_extension', $config);
        $config = $this->deleteProperty('sources.default.encode', $config);

        $cases['no optional arguments'] = [
            $config
        ];

        $config = $this->getConfiguration();
        $cases['all arguments set'] = [
            $config
        ];

        return $cases;
    }

    /**
     * Creates test cases with valid values for the sources.*.root_path property.
     *
     * @return array
     */
    public function validRootPathPropertyProvider(): array
    {
        $cases = [];
        $cases = array_merge($cases, $this->validPaths('Linux', '/', '/'));
        $cases = array_merge($cases, $this->validPaths('Windows', '\\', 'C:'));

        return $cases;
    }

    /**
     * Creates test cases with invalid values for the sources.*.encode property.
     *
     * @return array
     */
    public function invalidEncodeProperty(): array
    {
        if (!defined('PROPERTIES_ENCODE')) {
            define('PROPERTIES_ENCODE', 'The properties must match schema: encode');
        }

        $this->defineErrorConstants();

        $cases = [];

        $config = $this->createConfiguration('sources.default.encode', [
            'mime-type' => 'application/json',
            'encoder' => 'ubjson'
        ]);

        $cases['sources.*.encode property is not an array'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE,
                PROPERTIES_ENCODE,
                'The data (object) must match the type: array'
            ])
        ];

        $config = $this->createConfiguration('sources.default.encode', []);

        $cases['sources.*.encode property is an empty array'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE,
                PROPERTIES_ENCODE,
                'Array should have at least 1 items, 0 found'
            ])
        ];

        $config = $this->createConfiguration('sources.default.encode', [
            [
                'mime-type' => 'application-json',
                'encoder' => 'ubjson'
            ]
        ]);

        $cases['sources.*.encode.*.mime-type is invalid'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE,
                PROPERTIES_ENCODE,
                ARRAY_ITEMS,
                'The properties must match schema: mime-type',
                'The string should match pattern: (\w+|\*)/(\w+|\*)?',
            ])
        ];

        $config = $this->createConfiguration('sources.default.encode', [
            [
                'encoder' => 'ubjson'
            ]
        ]);

        $cases['sources.*.encode.*.mime-type property is missing'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE,
                PROPERTIES_ENCODE,
                ARRAY_ITEMS,
                'The required properties (mime-type) are missing',
            ])
        ];

        $config = $this->createConfiguration('sources.default.encode', [
            [
                'mime-type' => 'application/json',
            ]
        ]);

        $cases['sources.*.encode.*.encoder property is missing'] = [
            $config,
            $this->composeErrorMessage([
                ERROR_MESSAGE,
                SOURCES_ERROR_MESSAGE,
                OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE,
                PROPERTIES_ENCODE,
                ARRAY_ITEMS,
                'The required properties (encoder) are missing',
            ])
        ];

        return $cases;
    }

    /**
     * Creates test cases with valid Linux and Windows file paths.
     *
     * @param string $os The name of the operating system for whom the work is done
     * @param string $directorySeparator The OS specific directory separator '/' or '\'
     * @param string $absolutePath The starting point for an absolute file path. '/' in
     *    case of Linux and a drive letter followed by a colon in case of Windows.
     *
     * @return array
     */
    private function validPaths(string $os, string $directorySeparator, string $absolutePath): array
    {
        $data = [
            ['message' => "valid absolute {$os} file path", 'path' => $this->buildAbsolutePath($directorySeparator, $absolutePath)],
            ['message' => "valid absolute {$os} file path (containing '..').", 'path' => implode($directorySeparator, [$absolutePath, 'data', '..', 'default'])],
            ['message' => "valid absolute Linux file path (containing a hyphen).", 'path' => implode($directorySeparator, [$absolutePath, 'data', 'file-path', 'default'])],
        ];

        $relativeStartingPoints =[
            '.',
            '..',
            'fweidinger'
        ];

        foreach ($relativeStartingPoints as $startingPoint) {
            $data[] = [
                'message' => str_replace('XXX', $startingPoint, "valid relative {$os} file path (starting with 'XXX')."),
                'path' => implode($directorySeparator, [$startingPoint, 'data', 'default'])
            ];
        }

        $cases = [];

        foreach ($data as $item) {
            $config = $this->createConfiguration('sources.default.root_path', $item['path']);
            $cases[$item['message']] = [$config];
        }

        return $cases;
    }

    /**
     * Builds Linux/Windows absolute file paths. In case of a Linux path it replaces the // at
     * the start with a single slash.
     *
     * @param string $directorySeparator The OS specific directory separator '/' or '\'
     * @param string $absolutePath The starting point for an absolute file path. '/' in
     *    case of Linux and a drive letter followed by a colon in case of Windows.
     *
     * @return string
     */
    private function buildAbsolutePath(string $directorySeparator, string $absolutePath): string
    {
        $path = implode($directorySeparator, [$absolutePath, 'data', 'default']);

        return preg_replace('/^\/{2}/', '/', $path);
    }
}
