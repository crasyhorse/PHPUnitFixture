<?php

declare(strict_types=1);

namespace CrasyHorse\Testing\Encoder;

/**
 * Defines all necessary methods for classes of type Encoder.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
interface EncoderContract
{
    /**
     * Encodes the given content to a new format.
     *
     * @param array $content The content to encode
     *
     * @return array
     * @throws \CrasyHorse\Testing\Exceptions\UnableToEncodeException
     */
    public function encode(array $content): array;
}
