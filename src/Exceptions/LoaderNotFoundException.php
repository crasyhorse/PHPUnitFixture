<?php

declare(strict_types=1);

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
    public function __construct(string $driver, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Could not find a loader for the {$driver} driver.", $code, $previous);
    }
}
