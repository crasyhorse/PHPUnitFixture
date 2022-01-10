<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit\Config;

/**
 * Provides the helper method composeErrorMessage that composes an array of strings into
 * a multi-line error message.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
trait ComposeErrorMessage
{
    /**
     * Composes an array of strings into a multi-line error message
     *
     * @param array $messages
     *
     * @return string
     */
    private function composeErrorMessage(array $messages): string
    {
        return implode(
            PHP_EOL,
            $messages
        );
    }

    /**
     * Defines a list of common error messages.
     *
     * @return void
     */
    private function defineErrorConstants(): void
    {
        if (!defined('ERROR_MESSAGE')) {
            define('ERROR_MESSAGE', 'Your configuration object is malformed. Please check it!');
        }

        if (!defined('OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE')) {
            define('OBJECT_PROPERTIES_MATCH_ERROR_MESSAGE', "Object properties that match pattern '^[A-Za-z0-9]+$' must also match pattern's schema");
        }

        if (!defined('ARRAY_ITEMS')) {
            define('ARRAY_ITEMS', 'All array items must match schema');
        }
    }
}
