<?php

namespace Twoscore23\LaravelBetterMakes;

use Illuminate\Support\ServiceProvider;
use Twoscore23\LaravelBetterMakes\Commands\CustomControllerMake;
use Twoscore23\LaravelBetterMakes\Commands\CustomFactoryMake;
use Twoscore23\LaravelBetterMakes\Commands\CustomMigrationMake;
use Twoscore23\LaravelBetterMakes\Commands\CustomModelMake;
use Twoscore23\LaravelBetterMakes\Commands\CustomResourceMake;
use Twoscore23\LaravelBetterMakes\Commands\MakeControllerCombo;
use Twoscore23\LaravelBetterMakes\Commands\MakeFullModel;
use Twoscore23\LaravelBetterMakes\Commands\MakeService;

class LaravelBetterMakesProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CustomControllerMake::class,
                CustomFactoryMake::class,
                CustomMigrationMake::class,
                CustomModelMake::class,
                CustomResourceMake::class,
                MakeControllerCombo::class,
                MakeFullModel::class,
                MakeService::class
            ]);
        }
    }
}