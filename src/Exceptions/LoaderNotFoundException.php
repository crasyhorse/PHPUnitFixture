<?php

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

/**
 * This exception is thrown if a required Loader does not exist.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class LoaderNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, string $drivername = null)
    {
        $this->message = $message;
        
        if (empty($message)) {
            $driver = $drivername ?? 'given';
            $this->message = "Could not find a loader for the {$driver} driver.";
        }
    }
}
