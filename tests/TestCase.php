<?php

namespace Iak\Key\Tests;

use Iak\Key\KeyServiceProvider;
use Orchestra\Testbench\Bootstrap\HandleExceptions;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            KeyServiceProvider::class,
        ];
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        HandleExceptions::flushState($this);

        return $app;
    }
}
