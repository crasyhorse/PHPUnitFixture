<?php

namespace CrasyHorse\Testing;

use CrasyHorse\Testing\Exceptions\SourceNotFoundException;
use CrasyHorse\Testing\Reader\Reader;

class Fixture
{
    use Config;

    /**
     * The contents of the fixture(s) in an array.
     *
     * @var array|null
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
        $this->content = [];

        if (!empty($config)) {
            $this->configuration = $this->validate($config);
        }

        $this->source = $this->getDefaultSource();

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
            $this->content[] = json_decode(Reader::read($path, $this->source), true);
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
        $this->source = $this->config("sources.{$sourcename}") ?? $this->getDefaultSource();

        return $this;
    }

    /**
     * Returns the file's content as array. If the file has been empty, an empty
     * array will be returned.
     */
    public function toArray(): array
    {
        $value = [];
        foreach ($this->content as $fixture) {
            $value = array_merge_recursive($value, $fixture);
        }

        return $value;
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
