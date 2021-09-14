<?php

namespace CrasyHorse\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use CrasyHorse\Testing\LaravelTestingFixtureServiceProvider;

class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
