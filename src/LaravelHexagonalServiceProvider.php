<?php

namespace Koshuang\LaravelHexagonal;

use Illuminate\Support\ServiceProvider;
use Koshuang\LaravelHexagonal\Console\InstallCommand;
use Koshuang\LaravelHexagonal\Console\MakeModuleCommand;
use Koshuang\LaravelHexagonal\Console\ValidateCommand;

class LaravelHexagonalServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/hexagonal.php', 'hexagonal');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/hexagonal.php' => config_path('hexagonal.php'),
        ], 'hexagonal-config');

        $this->publishes([
            __DIR__ . '/../stubs/module' => base_path('stubs/hexagonal/module'),
            __DIR__ . '/../stubs/shared' => base_path('stubs/hexagonal/shared'),
            __DIR__ . '/../stubs/deptrac.yaml' => base_path('stubs/hexagonal/deptrac.yaml'),
        ], 'hexagonal-stubs');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                MakeModuleCommand::class,
                ValidateCommand::class,
            ]);
        }
    }
}
