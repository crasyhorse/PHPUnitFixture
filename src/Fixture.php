<?php

namespace CrasyHorse\Testing;

use CrasyHorse\Testing\Exceptions\SourceNotFoundException;
use CrasyHorse\Testing\Reader\Reader;
use CrasyHorse\Testing\Config\Config;
use CrasyHorse\Testing\Exceptions\InvalidArgumentException;

/**
 * This is the main class of crasyhorse/phpunit-fixture. It hold the
 * fixture method which should be used to load and process fixture
 * files.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class Fixture
{
    /**
     * The contents of the fixture(s).
     *
     * @var \CrasyHorse\Testing\Content
     */
    private $content;

    /**
     * The source to read the fixture from.
     *
     * @var string
     */
    private $source;

    /**
     * Validates the configuration object given by the user.
     *
     * @param array $configuration The custom configuration object
     */
    public function __construct(array $configuration = [])
    {
        Config::getInstance($configuration);

        $this->content = new Content();
        $this->source = 'default';
    }

    /**
     *
     * Loads and processes a fixture. It returns the contents of a fixture as
     * an object of type \CrasyHorse\Testing\Content.
     *
     * @param string|array $fixture Path to a single fixture file or an array
     *    containing a list of filenames
     *
     * @return \CrasyHorse\Testing\Content
     */
    public function fixture($fixture): Content
    {
        $fixtures = $this->resolveFixture($fixture);

        /** @var string $path */
        foreach ($fixtures as $path) {
            $value = Reader::read($path, $this->source);
            $this->content->add($value);
        }

        return $this->content;
    }

    /**
     * Sets the source object from where the fixture data should be loaded.
     *
     * @param string $sourcename The name of the source object, e. g. 'alternative'
     *
     * @return \CrasyHorse\Testing\Fixture
     * @throws \CrasyHorse\Testing\Exceptions\SourceNotFoundException
     */
    public function source(string $sourcename): self
    {
        $this->source = $sourcename;

        if (empty(Config::getInstance()->get("sources.{$sourcename}"))) {
            throw new SourceNotFoundException($sourcename);
        }

        return $this;
    }

    /**
     * If $fixture is a string this method converts it into an array.
     *
     * @param string|array $fixture Path to a single fixture file or an array
     *    containing a list of filenames
     *
     * @return array
     * @throws \CrasyHorse\Testing\Exceptions\InvalidArgumentException
     */
    private function resolveFixture($fixture): array
    {
        if (empty($fixture)) {
            throw new InvalidArgumentException('$fixture must be a string or an array!');
        }

        if (is_string($fixture)) {
            return [$fixture];
        }

        /** @psalm-suppress RedundantConditionGivenDocblockType */
        if (is_array($fixture)) {
            return $fixture;
        }

        throw new InvalidArgumentException('$fixture must be a string or an array!');
    }
}
