<?php

namespace Joemires\Superban\Tests;

use Joemires\Superban\SuperbanServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->get('router')->middleware('superban')->get('/superban-protected', function () {
            return response('I am protected by superban');
        });

        $this->app->get('router')->get('/superban-unprotected', function () {
            return response('I am not protected by superban');
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            SuperbanServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
