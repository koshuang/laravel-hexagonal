<?php

namespace Koshuang\LaravelHexagonal\Console;

use Illuminate\Console\Command;
use Koshuang\LaravelHexagonal\Support\ModuleScaffolder;

class MakeModuleCommand extends Command
{
    protected $signature = 'hexagonal:make-module {name : The module name} {--force : Overwrite generated files}';

    protected $description = 'Create a module with Domain, Application, and Infrastructure layers';

    public function handle(ModuleScaffolder $scaffolder): int
    {
        $written = $scaffolder->scaffold(
            (string) $this->argument('name'),
            (string) config('hexagonal.modules_path', base_path('Modules')),
            (string) config('hexagonal.modules_namespace', 'Modules'),
            dirname(__DIR__, 2) . '/stubs/module',
            (bool) $this->option('force'),
        );

        $this->info("Module created. {$written} files written.");

        return self::SUCCESS;
    }
}
