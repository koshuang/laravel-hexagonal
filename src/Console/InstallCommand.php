<?php

namespace Koshuang\LaravelHexagonal\Console;

use Illuminate\Console\Command;
use Koshuang\LaravelHexagonal\Support\ProjectFileWriter;

class InstallCommand extends Command
{
    protected $signature = 'hexagonal:install {--force : Overwrite files that already exist}';

    protected $description = 'Install the modular hexagonal architecture into this Laravel application';

    public function handle(ProjectFileWriter $writer): int
    {
        $packagePath = dirname(__DIR__, 2);
        $force = (bool) $this->option('force');

        $writer->copyFile(
            $packagePath . '/config/hexagonal.php',
            config_path('hexagonal.php'),
            $force,
        );
        $writer->copyDirectory(
            $packagePath . '/stubs/shared',
            base_path('Modules/Shared'),
            $force,
        );
        $writer->copyFile(
            $packagePath . '/stubs/deptrac.yaml',
            base_path('deptrac.yaml'),
            $force,
        );
        $writer->addModulesAutoload(base_path('composer.json'), $force);

        $this->info('Laravel Hexagonal Architecture installed.');
        $this->line('Run "composer dump-autoload" before creating your first module.');
        $this->line('Create a module with: php artisan hexagonal:make-module <Name>');

        return self::SUCCESS;
    }
}
