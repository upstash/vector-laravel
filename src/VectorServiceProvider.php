<?php

namespace Upstash\Vector\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Upstash\Vector\Contracts\IndexInterface;
use Upstash\VectorLaravel\Commands\VectorLaravelCommand;

class VectorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('vector')
            ->hasConfigFile('vector');
    }

    public function packageRegistered(): void
    {
        $this->app->bind('vector.manager', function (Application $app) {
            return new VectorManager($app);
        });

        $this->app->bind(IndexInterface::class, function (Application $app) {
            return new VectorManager($app);
        });
    }
}
