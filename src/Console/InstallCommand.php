<?php

namespace Koshuang\LaravelHexagonal\Console;

use Illuminate\Console\Command;
use Koshuang\LaravelHexagonal\Support\ProjectFileWriter;

class InstallCommand extends Command
{
    /** @var string */
    protected $signature = 'hexagonal:install {--force : Overwrite files that already exist}';

    /** @var string */
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
        $composerPath = base_path('composer.json');
        $writer->addModulesAutoload($composerPath, $force);
        $writer->addDevDependency($composerPath, 'deptrac/deptrac', '^4.7', $force);

        $this->info('Laravel Hexagonal Architecture installed.');
        $this->line('Run "composer update deptrac/deptrac" to install the architecture validation tools.');
        $this->line('Run "composer dump-autoload" before creating your first module.');
        $this->line('Create a module with: php artisan hexagonal:make-module <Name>');
        $this->line('Validate architecture with: php artisan hexagonal:validate');

        return self::SUCCESS;
    }
}
