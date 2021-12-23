<?php

namespace CrasyHorse\Testing;

use CrasyHorse\Testing\Exceptions\SourceNotFoundException;
use CrasyHorse\Testing\Reader\Reader;
use CrasyHorse\Testing\Fixture\Getter;
use CrasyHorse\Testing\Fixture\Unwrap;

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
    use Config;
    use Getter;
    use Unwrap;

    /**
     * The contents of the fixture(s) in an array.
     *
     * @var mixed
     */
    protected $content;

    /**
     * The source to read the fixture from.
     *
     * @var array|null
     */
    protected $source;

    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->configuration = $this->validate($config);
        }

        $this->source = $this->getDefaultSource();
        $this->content = [];

        if (!$this->source) {
            throw new SourceNotFoundException('Could not find the default source. Please create it.');
        }
    }

    /**
     * Loads and processes a fixture. It returns an object of type
     * \CrasyHorse\Testing\Fixture. The file contents can be returned via
     * toArray or toJson.
     *
     * @param mixed $fixture Path to a single fixture file or an array
     *                       containing a list of filenames
     *
     * @return \CrasyHorse\Testing\Fixture
     */
    public function fixture($fixture): self
    {
        $fixtures = $fixture;

        if (is_string($fixture)) {
            $fixtures = [];
            $fixtures[] = $fixture;
        }

        foreach ($fixtures as $path) {
            $value = Reader::read($path, $this->source);
            $this->addToContent($value);
        }

        return $this;
    }

    /**
     * Sets the source object from where the fixture should be loaded.
     *
     * @param string $sourcename The name of the source object, e. g. 'alternative'
     *
     * @return \CrasyHorse\Testing\Fixture
     */
    public function source(string $sourcename): self
    {
        $this->source = $this->config("sources.{$sourcename}");

        if (!$this->source) {
            throw new SourceNotFoundException();
        }

        return $this;
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
     * Addes a new value to Fixture.content
     *
     * @param array $value The value to be added to the contents.
     *
     * @return void
     * @throws \CrasyHorse\Testing\Exceptions\InvalidArgumentException
     */
    protected function addToContent(array $value): void
    {
        $this->content = array_merge_recursive($this->content, $value);
    }

    /**
     * Returns the default source if it is configured. Otherwise,
     * it returns null.
     *
     * @return array|null
     */
    protected function getDefaultSource()
    {
        return $this->config('sources.default');
    }
}
