<?php

declare(strict_types=1);

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

/**
 * May be thrown if an encoder class is not able to transform the given content.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
class UnableToEncodeException extends Exception
{
    public function __construct(string $format, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('', $code, $previous);

        $this->message = "The given content could not be encoded to {$format}";
    }
}
