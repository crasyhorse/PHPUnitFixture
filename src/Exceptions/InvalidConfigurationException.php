<?php

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

/**
 * This exception is thrown if the configuration object is malformed.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class InvalidConfigurationException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->message = $message;
        
        if (empty($message)) {
            $this->message = 'Your configuration object is malformed. Please check it!';
        }
    }
}
