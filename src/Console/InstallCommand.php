<?php

namespace Koshuang\LaravelHexagonal\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Koshuang\LaravelHexagonal\Support\DeptracFileRenderer;
use Koshuang\LaravelHexagonal\Support\ProjectFileWriter;
use Koshuang\LaravelHexagonal\Support\StubPathResolver;

class InstallCommand extends Command
{
    /** @var string */
    protected $signature = 'hexagonal:install {--force : Overwrite files that already exist}';

    /** @var string */
    protected $description = 'Install the modular hexagonal architecture into this Laravel application';

    public function handle(
        ProjectFileWriter $writer,
        StubPathResolver $stubPaths,
        DeptracFileRenderer $deptracRenderer,
        Filesystem $files,
    ): int {
        $packagePath = dirname(__DIR__, 2);
        $force = (bool) $this->option('force');

        $writer->copyFile(
            $packagePath . '/config/hexagonal.php',
            config_path('hexagonal.php'),
            $force,
        );
        $writer->copyDirectory(
            $stubPaths->shared(),
            base_path('Modules/Shared'),
            $force,
        );

        $deptracPath = base_path('deptrac.yaml');
        if (! $files->exists($deptracPath) || $force) {
            $renderedDeptrac = $deptracRenderer->render(
                allowCarbon: $this->allowance('carbon'),
                allowIlluminateSupportFacades: $this->allowance('illuminate_support_facades'),
                allowIlluminate: $this->allowance('illuminate'),
            );
            $files->ensureDirectoryExists(dirname($deptracPath));
            $files->put($deptracPath, $renderedDeptrac);
        }
        $composerPath = base_path('composer.json');
        $writer->addRuntimeDependency($composerPath, 'nwidart/laravel-modules', '^13.0', $force);
        $writer->addModulesAutoload($composerPath, $force);
        $writer->addDevDependency($composerPath, 'deptrac/deptrac', '^4.7', $force);
        $writer->allowComposerPlugin($composerPath, 'wikimedia/composer-merge-plugin', $force);

        $this->info('Laravel Hexagonal Architecture installed.');
        $this->line(
            'Run "composer update nwidart/laravel-modules deptrac/deptrac" to install architecture dependencies.',
        );
        $this->line('Run "composer dump-autoload" before creating your first module.');
        $this->line('Create a module with: php artisan hexagonal:make-module <Name>');
        $this->line('Validate architecture with: php artisan hexagonal:validate');

        return self::SUCCESS;
    }

    private function allowance(string $key): bool
    {
        return (bool) config("hexagonal.deptrac.allowances.{$key}", true);
    }
}
