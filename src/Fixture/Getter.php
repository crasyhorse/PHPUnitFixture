<?php

namespace CrasyHorse\Testing\Fixture;

use Adbar\Dot;

/**
 * This trait implements the possibility to address values in the "content[]" array
 * whith the help of the Array-Dot-Notation.
 *
 * @author Florian Weidinger
 * @since 0.2.0
 */
trait Getter
{
    /**
     * Returns a value from the fixtures content. This method is overloaded
     * two times:
     *
     * $fixture->get('dot.notation.to.array.value');
     *
     * Returns a single string value from "content[]" array. Use Array-Dot-Notation
     * to address a value. If the addressed values does not exist, "get" returns
     * null.
     *
     * $fixture->get();
     *
     * Returns the whole "content[]" array.
     *
     * @param array $args Splat parameter for dynamic argument evaluation.
     *
     * @return mixed
     */
    public function get(...$args)
    {
        if (count($args) === 1 && is_string($args[0])) {
            return $this->getFromArray($args[0]);
        }

        return $this->content;
    }
    
    /**
     * Returns an element from the fixture if "content" is of
     * type array.
     *
     * @param string $dotnotation Array access by dot notation.
     *
     * @return string|null
     */
    protected function getFromArray(string $dotnotation)
    {
        if (!is_array($this->content) || empty($this->content)) {
            return null;
        }

        $dot = new Dot($this->content);

        return $dot->get($dotnotation);
    }
}
