<?php

namespace DaniloPolani\JsonValidation\Tests;

use DaniloPolani\JsonValidation\JsonValidationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            JsonValidationServiceProvider::class,
        ];
    }
}
