<?php

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

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
