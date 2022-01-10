<?php

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

/**
 * This exception is thrown if the required source could not be found. That is, there is
 * no valid default source.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class SourceNotFoundException extends Exception
{
    public function __construct(string $source, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('', $code, $previous);

        $this->message = "The selected source '{$source}' could not be found. Please configure it or use the default source.";
    }
}
