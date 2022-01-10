<?php

declare(strict_types=1);

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

/**
 * This exception is thrown if either the selected encoder could not be found
 * within the list of encoders in the configuration object or if the selected
 * encoder is not valid for the given mime-type.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
class InvalidEncodingException extends Exception
{
    public function __construct(string $encoderAlias, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Invalid encoding. Either the encoder {$encoderAlias} could not be found in the encoders-list or the selected encoding is not valid for the given mime-type.", $code, $previous);
    }
}
