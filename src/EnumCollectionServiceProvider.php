<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections;

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
        $package->name('laravel-enum-collections');

        $this->publishes([
            $this->package->basePath('/../stubs/LaravelEnumCollectionModelIdeHelperHook.stub')
                => app_path('Support/IdeHelper/LaravelEnumCollectionModelIdeHelperHook.php'),
        ], 'laravel-enum-collections-ide-helper-hooks');
    }
}
