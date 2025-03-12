<?php

namespace Upstash\Vector\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Upstash\Vector\Contracts\IndexInterface;
use Upstash\Vector\Laravel\Commands\VectorIndexResetCommand;
use Upstash\Vector\Laravel\Commands\VectorInfoCommand;
use Upstash\Vector\Laravel\Commands\VectorNamespaceDeleteCommand;
use Upstash\Vector\Laravel\Commands\VectorNamespaceListCommand;

class VectorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('vector')
            ->hasConfigFile('vector')
            ->hasCommands([
                VectorInfoCommand::class,
                VectorNamespaceListCommand::class,
                VectorNamespaceDeleteCommand::class,
                VectorIndexResetCommand::class,
            ]);
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
