<?php

namespace Homeful\Availments;

use Homeful\Availments\Commands\AvailmentsCommand;
use Homeful\Availments\Providers\EventServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AvailmentsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('availments')
            ->hasConfigFile(['availments', 'property', 'data', 'equity', 'loan'])
            ->hasViews()
            ->publishesServiceProvider(EventServiceProvider::class)
            ->hasMigration('create_availments_table')
            ->hasCommand(AvailmentsCommand::class);
    }
}
