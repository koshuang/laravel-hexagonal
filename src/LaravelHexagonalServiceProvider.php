<?php

namespace Koshuang\LaravelHexagonal;

use Illuminate\Support\ServiceProvider;
use Koshuang\LaravelHexagonal\Console\InstallCommand;
use Koshuang\LaravelHexagonal\Console\MakeModuleCommand;

class LaravelHexagonalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/hexagonal.php', 'hexagonal');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/hexagonal.php' => config_path('hexagonal.php'),
        ], 'hexagonal-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                MakeModuleCommand::class,
            ]);
        }
    }
}
