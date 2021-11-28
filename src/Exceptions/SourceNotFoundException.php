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
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->message = $message;
        
        if (empty($message)) {
            $this->message = 'Neither a source nor a default source could be found. Please configure at least a default source.';
        }
    }
}
