<?php

namespace CrasyHorse\Testing\Fixture;

use Adbar\Dot;
use CrasyHorse\Testing\Exceptions\InvalidArgumentException;

/**
 *
 * @author Florian Weidinger
 * @since 0.2.0
 */
trait Unwrap
{
    /**
     * Returns the value of $elementToUnwrap. It unwraps the "content[]" array
     * from the given array element. Use Array-Dot-Notation to define the element to
     * unwrap from.
     *
     * This method throws an InvalidArgumentException if the value of $elementToUnwrap
     * is not of type array.
     *
     * If $elementToUnwrap is null, unwrap looks for a "data" element and unwraps
     * "content[]" array from data. If no "data" element exists the whole
     * "content[]" array is returned.
     *
     * @param string|null $elementToUnwrap
     *
     * @return \CrasyHorse\Testing\Fixture
     * @throws \CrasyHorse\Testing\Exceptions\InvalidArgumentException
     */
    public function unwrap(string $elementToUnwrap = null): self
    {
        if (empty($elementToUnwrap)) {
            $this->content = $this->unwrapData();

            return $this;
        }

        $value = $this->get($elementToUnwrap);

        if (!is_array($value) && !is_object($value)) {
            throw new InvalidArgumentException('The element to unwrap must exist and it must be of type array or object.');
        }

        $this->content = $value;

        return $this;
    }

    /**
     * Looks for a "data" element and unwraps "content[]" array from data.
     * If no "data" element exists the whole "content[]" array is returned.
     *
     * @return array
     */
    protected function unwrapData(): array
    {
        $value = $this->get('data');

        if (empty($value)) {
            return $this->content;
        }

        return $value;
    }
}
