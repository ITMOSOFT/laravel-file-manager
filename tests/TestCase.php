<?php

namespace ItmoSoft\FileManager\Tests;

use ItmoSoft\FileManager\FileManagerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [FileManagerServiceProvider::class];
    }
}
