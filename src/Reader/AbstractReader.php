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
     * The encoder to be used to encode file fixture's contents.
     *
     * @var EncoderContract
     */
    protected $encoder;

    /**
     * @var string
     */
    protected $source;

    /**
     * @param string $source The name of the Config.source object to use for loading the fixture
     */
    public function __construct(string $source)
    {
        $this->source = $source;

        $encode = Config::getInstance()->get("sources.{$source}.encode");

        if ($this->sourceHasEncodings()) {
            $encodings = (new ArrayObject($encode))->getIterator();
            $this->initEncoder($encodings);
        }
    }
    /**
     * @inheritdoc
     */
    public function read(File $file): array
    {
        $content = $this->doRead($file);

        if ($this->encoder) {
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
        $encoding = $encodings->current();

        while ($encodings->valid() && $encoding['mime-type'] !== static::MIME_TYPE) {
            $encodings->next();
            $encoding = $encodings->current();
        }

        if ($encoding) {
            try {
                $encoderClass = Config::getInstance()->get("encoders.{$encoding['encoder']}");
                $this->encoder = (new ReflectionClass($encoderClass))->newInstanceArgs();
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
        return array_key_exists('encode', Config::getInstance()->get("sources.{$this->source}"));
    }

    /**
     * Does all the work necessary work to read a file (e. g. decompress it or convert it to JSON)
     *
     * @param \CrasyHorse\Testing\Loader\File $file
     *
     * @return array
     */
    abstract protected function doRead(File $file): array;
}
