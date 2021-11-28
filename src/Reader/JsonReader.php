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
        if (in_array(strToLower($file->getMimeType() ?? ''), ['text/plain', strToLower($this::TYPE)])) {
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
}
