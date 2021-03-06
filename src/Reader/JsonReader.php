<?php

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Loader\File;
use pcrov\JsonReader\JsonReader as JsonDecoder;
use CrasyHorse\Testing\Config\Config;

/**
 * This class is responsible for reading and parsing fixture files with
 * JSON content.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class JsonReader extends AbstractReader
{
    /**
     * The mime type the Reader class is responsible for.
     *
     * @var string
     */
    public const MIME_TYPE='application/json';

    /**
     * The Json parser used to parse fixtures.
     *
     * @var \pcrov\JsonReader\JsonReader
     */
    private $decoder;

    /**
     * @param string $source The name of the Config.source object to use for loading the fixture
     *
     * @param \CrasyHorse\Testing\Config\Config $configuration
     */
    public function __construct(string $source, Config $configuration)
    {
        parent::__construct($source, $configuration);
        $this->decoder = new JsonDecoder();
    }

    /**
     * Uses mime types or regex to validate whether the Reader is responsible for this type
     * of content or not.
     *
     * The regex below is taken from @link{https://regex101.com/r/tA9pM8/1/codegen?language=php}.
     *
     * @param string $content The content string to validate
     *
     * @return bool
     */
    public function isValid(string $content): bool
    {
        $regEx = '/(?(DEFINE)
        (?<json>(?>\s*(?&object)\s*|\s*(?&array)\s*))
        (?<object>(?>\{\s*(?>(?&pair)(?>\s*,\s*(?&pair))*)?\s*\}))
        (?<pair>(?>(?&STRING)\s*:\s*(?&value)))
        (?<array>(?>\[\s*(?>(?&value)(?>\s*,\s*(?&value))*)?\s*\]))
        (?<value>(?>true|false|null|(?&STRING)|(?&NUMBER)|(?&object)|(?&array)))
        (?<STRING>(?>"(?>\\\\(?>["\\\\\/bfnrt]|u[a-fA-F0-9]{4})|[^"\\\\\0-\x1F\x7F]+)*"))
        (?<NUMBER>(?>-?(?>0|[1-9][0-9]*)(?>\.[0-9]+)?(?>[eE][+-]?[0-9]+)?))
        )
        \A(?&json)\z/x';

        preg_match($regEx, $content, $matches, PREG_OFFSET_CAPTURE, 0);

        return count($matches) > 0;
    }

    /**
     * Does all the work necessary work to read a file (e. g. decompress it or convert it to JSON)
     *
     * @param \CrasyHorse\Testing\Loader\File $file
     *
     * @return array|null
     */
    protected function doRead(File $file)
    {
        return $this->decode($file->getContent());
    }

    /**
     * Parses the Json string with pcrov/JsonReader and returns the contents
     * as array.
     *
     * @param string $json The Json string to parse
     *
     * @return array|null
     */
    private function decode(string $json)
    {
        $this->decoder->json($json);
        $this->decoder->read();

        /** @var array|null $content */
        $content = $this->decoder->value() ?? [];
        $this->decoder->close();

        return $content;
    }
}
