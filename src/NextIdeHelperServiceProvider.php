<?php

namespace Soyhuce\NextIdeHelper;

use Illuminate\Support\ServiceProvider;
use Soyhuce\NextIdeHelper\Console\MacrosCommand;
use Soyhuce\NextIdeHelper\Console\ModelsCommand;

class NextIdeHelperServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../assets/config.php' => config_path('next-ide-helper.php'),
        ]);
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
                ModelsCommand::class,
            ]);
        }
    }
}
