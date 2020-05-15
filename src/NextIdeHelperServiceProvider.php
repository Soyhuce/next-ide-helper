<?php

namespace Soyhuce\NextIdeHelper;

use Illuminate\Support\ServiceProvider;
use Soyhuce\NextIdeHelper\Console\MacrosCommand;
use Soyhuce\NextIdeHelper\Console\MetaCommand;
use Soyhuce\NextIdeHelper\Console\ModelsCommand;

class NextIdeHelperServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes(
            [__DIR__ . '/../assets/config.php' => config_path('next-ide-helper.php')],
            ['next-ide-helper:config', 'config']
        );

        $this->publishes(
            [__DIR__ . '/../resources/views' => resource_path('views/vendor/next-ide-helper')],
            'next-ide-helper:views'
        );

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'next-ide-helper');
    }

    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->mergeConfigFrom(
                __DIR__ . '/../assets/config.php',
                'next-ide-helper'
            );

            $this->commands([
                MacrosCommand::class,
                MetaCommand::class,
                ModelsCommand::class,
            ]);
        }
    }
}
