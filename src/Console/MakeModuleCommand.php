<?php

namespace Koshuang\LaravelHexagonal\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Koshuang\LaravelHexagonal\Support\ModuleScaffolder;
use Koshuang\LaravelHexagonal\Support\ModuleStatusRegistry;
use Koshuang\LaravelHexagonal\Support\StubPathResolver;
use RuntimeException;

class MakeModuleCommand extends Command
{
    /** @var string */
    protected $signature = 'hexagonal:make-module
                            {name : The module name}
                            {--force : Overwrite generated files}
                            {--no-active : Do not enable the module in modules_statuses.json}
                            {--stub-path= : Use a custom module stub directory}';

    /** @var string */
    protected $description = 'Create a module with Domain, Application, and Infrastructure layers';

    public function handle(
        ModuleScaffolder $scaffolder,
        StubPathResolver $stubPaths,
        Filesystem $files,
    ): int {
        $moduleArgument = $this->argument('name');
        $modulesPath = config('hexagonal.modules_path', base_path('Modules'));
        $modulesNamespace = config('hexagonal.modules_namespace', 'Modules');
        $statusesPath = config('hexagonal.modules_statuses', base_path('modules_statuses.json'));

        if (! is_string($moduleArgument) || ! is_string($modulesPath) || ! is_string($modulesNamespace) || ! is_string($statusesPath)) {
            throw new RuntimeException('Hexagonal module configuration and name must contain string values.');
        }

        $moduleName = Str::studly($moduleArgument);

        if (! preg_match('/^[A-Z][A-Za-z0-9]*$/D', $moduleName)) {
            $this->error("Invalid module name: {$moduleArgument}");

            return self::FAILURE;
        }

        $modulePath = rtrim($modulesPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $moduleName;

        if ($files->isDirectory($modulePath) && ! $this->option('force')) {
            $this->error("Module already exists: {$moduleName}. Use --force to overwrite generated files.");

            return self::FAILURE;
        }

        $stubPath = $this->option('stub-path');

        $written = $scaffolder->scaffold(
            $moduleName,
            $modulesPath,
            $modulesNamespace,
            $stubPaths->module(is_string($stubPath) ? $stubPath : ''),
            (bool) $this->option('force'),
        );

        $registry = new ModuleStatusRegistry($files, $statusesPath);
        $registry->setActive($moduleName, ! $this->option('no-active'));

        $this->info("Module created. {$written} files written.");
        $this->line("Module {$moduleName} is " . ($this->option('no-active') ? 'not enabled' : 'enabled') . ' in modules_statuses.json.');

        return self::SUCCESS;
    }
}
