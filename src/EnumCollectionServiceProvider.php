<?php

namespace Datomatic\EnumCollection;

use Datomatic\EnumCollection\Commands\EnumCollectionCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EnumCollectionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-enum-collections')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-enum-collections_table')
            ->hasCommand(EnumCollectionCommand::class);
    }
}
