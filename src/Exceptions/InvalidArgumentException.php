<?php

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

/**
 * This exception is thrown if a methods gets an invalid argument.
 *
 * @author Florian Weidinger
 * @since 0.2.0
 */
class InvalidArgumentException extends Exception
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('A method has received an invalid argument value for one of its parameters.', $code, $previous);

        $this->message = sprintf('%s %s', parent::getMessage(), $message);
    }
}
