<?php

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Reader\AbstractReader;
use CrasyHorse\Testing\Loader\File;
use pcrov\JsonReader\JsonReader as JsonDecoder;

/**
 * This class is responsible for reading and parsing fixture files of
 * mime type "application/json" or "plain/text" because sometimes the
 * mime type of a Json file is not correctly guessed as "application/json"
 * and "text/plain" is used.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class JsonReader extends AbstractReader
{
    /**
     * The default mime type to read by JsonReader.
     *
     * @var string
     */
    const TYPE='application/json';

    /**
     * The Json parser used to parse fixtures.
     *
     * @var \pcrov\JsonReader\JsonReader
     */
    private $decoder;

    /**
     * @inheritdoc
     */
    protected function initReader(): void
    {
        $this->decoder = new JsonDecoder();
    }

    /**
     * {@inheritdoc}
     */
    protected function doRead(File $file): array
    {
        if ($this->isValidJson($file->getContent())) {
            return $this->decode($file->getContent());
        }

        return [];
    }

    /**
     * Parses the Json string with pcrov/JsonReader and returns the contents
     * as array.
     *
     * @param string $json The Json string to parse
     *
     * @return array
     */
    protected function decode(string $json): array
    {
        $this->decoder->json($json);
        $this->decoder->read();
        $content = $this->decoder->value() ?? [];
        $this->decoder->close();
        
        return $content;
    }

    /**
     * Uses complex regex to validate whether $json is a valid JSON string or not.
     *
     * The regex below is taken from @link{https://regex101.com/r/tA9pM8/1/codegen?language=php}.
     *
     * @param string $json The Json string to validate
     *
     * @return bool
     */
    protected function isValidJson(string $json): bool
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

        preg_match($regEx, $json, $matches, PREG_OFFSET_CAPTURE, 0);

        return count($matches) > 0;
    }
}
