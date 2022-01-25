<?php

namespace CrasyHorse\Testing\Reader;

use CrasyHorse\Testing\Reader\ReaderContract;
use CrasyHorse\Testing\Loader\File;
use CrasyHorse\Testing\Encoder\EncoderContract;
use ArrayObject;
use ArrayIterator;
use ReflectionClass;
use ReflectionException;
use CrasyHorse\Testing\Exceptions\InvalidEncodingException;
use CrasyHorse\Testing\Config\Config;

/**
 * Abstract base class for all Reader classes. It implements the "read" method
 * defined by CrasyHorse\Testing\Reader\ReaderContract and it also provides
 * the abstract method "doRead" every Reader has to implement.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
abstract class AbstractReader implements ReaderContract
{
    /**
     * @var \CrasyHorse\Testing\Config\Config $configuration
     */
    protected $configuration;

    /**
     * The encoder to be used to encode file fixture's contents.
     *
     * @var EncoderContract|null
     */
    protected $encoder;

    /**
     * @var string
     */
    protected $source;

    /**
     * @param string $source The name of the Config.source object to use for loading the fixture
     *
     * @param \CrasyHorse\Testing\Config\Config $configuration
     */
    public function __construct(string $source, Config $configuration)
    {
        $this->source = $source;
        $this->configuration = $configuration;

        /** @var array $encode */
        $encode = $configuration->get("sources.{$source}.encode");

        if ($this->sourceHasEncodings()) {
            /** @var ArrayIterator<array-key, object|null> */
            $encodings = (new ArrayObject($encode))->getIterator();
            $this->initEncoder($encodings);
        }
    }

    /**
     * Reads the contents of a {@link File}. The file is getting processor by the responsible Reader class.
     * The responsible reader is determined by the file type.
     *
     * @param \CrasyHorse\Testing\Loader\File $file The file to read.
     *
     * @return array|null
     */
    public function read(File $file)
    {
        $content = $this->doRead($file);

        if ($this->encoder && $content) {
            return $this->encoder->encode($content);
        }

        return $content;
    }

    /**
     * Returns true if the mime-type of the reader class is mentioned in the source's encode property.
     *
     * @return void
     */
    private function initEncoder(ArrayIterator $encodings): void
    {
        /** @var object|null $encoding */
        $encoding = $encodings->current();

        while ($encodings->valid() && $encoding['mime-type'] !== static::MIME_TYPE) {
            $encodings->next();
            /** @var object|null $encoding */
            $encoding = $encodings->current();
        }

        if ($encoding) {
            try {
                /** @var class-string $encoderClass */
                $encoderClass = $this->configuration->get("encoders.{$encoding['encoder']}");
                /** @var EncoderContract */
                $this->encoder = (new ReflectionClass($encoderClass))->newInstanceArgs([]);
            } catch (ReflectionException $e) {
                throw new InvalidEncodingException($encoding['encoder']);
            }
        }
    }

    /**
     * Returns true if there is an 'encode' property within the selected source object.
     *
     * @return bool
     */
    private function sourceHasEncodings(): bool
    {
        /** @var array $sourceObject */
        $sourceObject = $this->configuration->get("sources.{$this->source}");
        return array_key_exists('encode', $sourceObject);
    }

    /**
     * Does all the work necessary work to read a file (e. g. decompress it or convert it to JSON)
     *
     * @param \CrasyHorse\Testing\Loader\File $file
     *
     * @return array|null
     */
    abstract protected function doRead(File $file);
}
