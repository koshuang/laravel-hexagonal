<?php

namespace Koshuang\LaravelHexagonal\Console;

use Illuminate\Console\Command;
use Koshuang\LaravelHexagonal\Support\ModuleScaffolder;
use Koshuang\LaravelHexagonal\Support\StubPathResolver;
use RuntimeException;

class MakeModuleCommand extends Command
{
    /** @var string */
    protected $signature = 'hexagonal:make-module
                            {name : The module name}
                            {--force : Overwrite generated files}
                            {--stub-path= : Use a custom module stub directory}';

    /** @var string */
    protected $description = 'Create a module with Domain, Application, and Infrastructure layers';

    public function handle(ModuleScaffolder $scaffolder, StubPathResolver $stubPaths): int
    {
        $moduleArgument = $this->argument('name');
        $modulesPath = config('hexagonal.modules_path', base_path('Modules'));
        $modulesNamespace = config('hexagonal.modules_namespace', 'Modules');

        if (! is_string($moduleArgument) || ! is_string($modulesPath) || ! is_string($modulesNamespace)) {
            throw new RuntimeException('Hexagonal module configuration and name must contain string values.');
        }

        $stubPath = $this->option('stub-path');

        $written = $scaffolder->scaffold(
            $moduleArgument,
            $modulesPath,
            $modulesNamespace,
            $stubPaths->module(is_string($stubPath) ? $stubPath : ''),
            (bool) $this->option('force'),
        );

        $this->info("Module created. {$written} files written.");

        return self::SUCCESS;
    }
}
