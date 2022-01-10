<?php

declare(strict_types=1);

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

/**
 * This exception is thrown if there is no suitable reader configured
 * to read the given fixture.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
class NoSuitableReaderFoundException extends Exception
{
    public function __construct(string $filename, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("No suitable reader found to read fixture {$filename}", $code, $previous);
    }
}
