<?php

namespace Homeful\Availments\Tests;

use Homeful\Availments\Providers\EventServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Homeful\Availments\AvailmentsServiceProvider;
use Spatie\SchemalessAttributes\SchemalessAttributesServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Homeful\\Availments\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            AvailmentsServiceProvider::class,
            SchemalessAttributesServiceProvider::class,
            EventServiceProvider::class
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('data.validation_strategy', 'always');
        config()->set('data.max_transformation_depth', 5);
        config()->set('data.throw_when_max_transformation_depth_reached', 5);

        $migration = include __DIR__.'/../database/migrations/create_availments_table.php.stub';
        $migration->up();
    }
}
