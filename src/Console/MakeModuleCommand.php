<?php

namespace Koshuang\LaravelHexagonal\Console;

use Illuminate\Console\Command;
use Koshuang\LaravelHexagonal\Support\ModuleScaffolder;
use RuntimeException;

class MakeModuleCommand extends Command
{
    /** @var string */
    protected $signature = 'hexagonal:make-module {name : The module name} {--force : Overwrite generated files}';

    /** @var string */
    protected $description = 'Create a module with Domain, Application, and Infrastructure layers';

    public function handle(ModuleScaffolder $scaffolder): int
    {
        $moduleArgument = $this->argument('name');
        $modulesPath = config('hexagonal.modules_path', base_path('Modules'));
        $modulesNamespace = config('hexagonal.modules_namespace', 'Modules');

        if (! is_string($moduleArgument) || ! is_string($modulesPath) || ! is_string($modulesNamespace)) {
            throw new RuntimeException('Hexagonal module configuration and name must contain string values.');
        }

        $written = $scaffolder->scaffold(
            $moduleArgument,
            $modulesPath,
            $modulesNamespace,
            dirname(__DIR__, 2) . '/stubs/module',
            (bool) $this->option('force'),
        );

        $this->info("Module created. {$written} files written.");

        return self::SUCCESS;
    }
}
