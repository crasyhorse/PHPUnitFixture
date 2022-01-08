<?php

declare(strict_types=1);

namespace CrasyHorse\Testing\Encoder;

/**
 * Encodes (binary) strings to Base64.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
class Base64 implements EncoderContract
{
    /**
     * {@inheritdoc}
     */
    public function encode(array $content): array
    {
        $convertedContent = $content[0];
        return [base64_encode($convertedContent)];
    }
}
