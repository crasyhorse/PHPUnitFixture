<?php

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

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
