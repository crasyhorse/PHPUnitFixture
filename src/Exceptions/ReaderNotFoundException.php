<?php

declare(strict_types=1);

namespace CrasyHorse\Testing\Exceptions;

use Exception;
use Throwable;

/**
 * This exception is thrown if a required Reader does not exist.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
class ReaderNotFoundException extends Exception
{
    public function __construct(string $reader, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Could not find a reader for the {$reader} alias.", $code, $previous);
    }
}
