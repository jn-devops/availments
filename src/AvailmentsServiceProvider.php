<?php

namespace Homeful\Availments;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Homeful\Availments\Commands\AvailmentsCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_availments_table')
            ->hasCommand(AvailmentsCommand::class);
    }
}