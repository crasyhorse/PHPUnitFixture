<?php

declare(strict_types=1);

namespace CrasyHorse\Testing;

use Adbar\Dot;
use CrasyHorse\Testing\Exceptions\InvalidArgumentException;

/**
 * Represents the resolved content of a fixture.
 *
 * @author Florian Weidinger
 * @since 0.3.0
 */
class Content
{
    /**
     * The resolve content of the fixture(s).
     *
     * @var array
     */
    private $content;

    public function __construct()
    {
        $this->content = [];
    }

    /**
     * Adds a new value to contents.
     *
     * @param array $value The value to be added to the contents.
     *
     * @return void
     * @throws \CrasyHorse\Testing\Exceptions\InvalidArgumentException
     */
    public function add(array $value): void
    {
        $this->content = array_merge_recursive($this->content, $value);
    }

    /**
     * Provides the ability to address values in the "content[]" array
     * with the help of the Array-Dot-Notation. It is overloaded two times:
     *
     * $fixture->get('dot.notation.to.array.value');
     *
     * Returns a single string value from "content[]" array. Use Array-Dot-Notation
     * to address a value. If the addressed values does not exist, "get" returns
     * null.
     *
     * $fixture->get();
     *
     * Returns the complete resolved content.
     *
     * @param string|null $dotnotation Array access by dot notation.
     *
     * @return array|string
     */
    public function get(string $dotnotation = null)
    {
        if (empty($dotnotation)) {
            return $this->content;
        }

        $dot = new Dot($this->content);

        /** @var array|string */
        return $dot->get($dotnotation);
    }

    /**
    * Returns the file's content as array if possible. If the fixture file is empty
    * or if it is a binary file toArray returns an empty array.
    *
    * @return array
    */
    public function toArray(): array
    {
        return $this->content;
    }

    /**
     * Returns the file's content as JSON string or null if the file has been empty.
     *
     * @return string|null
     */
    public function toJson()
    {
        return json_encode($this->content);
    }

    /**
     * Returns the value of $elementToUnwrap. It unwraps the "content[]" array
     * from the given array element. Use Array-Dot-Notation to define the element to
     * unwrap from.
     *
     * Throws an InvalidArgumentException if the value of $elementToUnwrap
     * is not of type array.
     *
     * If $elementToUnwrap is null, unwrap looks for a "data" element and unwraps
     * "content[]" array from data. If no "data" element exists the whole
     * "content[]" array is returned.
     *
     * @param string|null $elementToUnwrap
     *
     * @return \CrasyHorse\Testing\Content
     * @throws \CrasyHorse\Testing\Exceptions\InvalidArgumentException
     */
    public function unwrap(string $elementToUnwrap = null): self
    {
        if (empty($elementToUnwrap)) {
            $this->content = $this->unwrapData();

            return $this;
        }

        $value = $this->get($elementToUnwrap);

        if (!is_array($value)) {
            throw new InvalidArgumentException('The element to unwrap must exist and it must be of type array or object.');
        }

        $this->content = $value;

        return $this;
    }

    /**
     * Looks for a "data" element and unwraps "content[]" array from data.
     * If no "data" element exists the complete "content[]" array is returned.
     *
     * @return array
     */
    private function unwrapData(): array
    {
        $value = $this->get('data');

        if (empty($value)) {
            return $this->content;
        }

        if (is_string($value)) {
            return [$value];
        }

        return $value;
    }
}
