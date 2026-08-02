<?php

namespace Koshuang\LaravelHexagonal\Support;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RuntimeException;

class ModuleScaffolder
{
    /**
     * @var array<int, string>
     */
    private const DIRECTORIES = [
        'Application/Port/In',
        'Application/Port/Out',
        'Application/Services',
        'Domain/Entities',
        'Domain/Services',
        'Domain/ValueObjects',
        'Infrastructure/Adapter/In/Console',
        'Infrastructure/Adapter/In/Web/Http/Controllers',
        'Infrastructure/Adapter/In/Web/Http/Middleware',
        'Infrastructure/Adapter/In/Web/Http/Requests',
        'Infrastructure/Adapter/In/Web/Routes',
        'Infrastructure/Adapter/Out/Persistence/Factories',
        'Infrastructure/Adapter/Out/Persistence/Migrations',
        'Infrastructure/Adapter/Out/Persistence/Models',
        'Infrastructure/Adapter/Out/Persistence/Repositories',
        'Infrastructure/Adapter/Out/Persistence/Seeders',
        'Infrastructure/Config',
        'Infrastructure/Providers',
        'Tests/Feature',
        'Tests/Unit',
    ];

    /**
     * @var array<string, string>
     */
    private const FILES = [
        'module.json' => 'module.json.stub',
        'composer.json' => 'composer.json.stub',
        'Infrastructure/Providers/MODULEServiceProvider.php' => 'module-service-provider.php.stub',
        'Infrastructure/Providers/DIServiceProvider.php' => 'di-service-provider.php.stub',
        'Infrastructure/Providers/RouteServiceProvider.php' => 'route-service-provider.php.stub',
        'Infrastructure/Adapter/In/Web/Routes/api.php' => 'api-routes.php.stub',
        'Infrastructure/Adapter/In/Web/Routes/web.php' => 'web-routes.php.stub',
        'Infrastructure/Config/config.php' => 'module-config.php.stub',
    ];

    public function __construct(private readonly Filesystem $files)
    {
    }

    public function scaffold(
        string $moduleName,
        string $modulesPath,
        string $modulesNamespace,
        string $stubPath,
        bool $force = false,
    ): int {
        $moduleName = Str::studly($moduleName);

        if ($moduleName === '') {
            throw new RuntimeException('Module name cannot be empty.');
        }

        $modulePath = rtrim($modulesPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $moduleName;
        $replacements = [
            '{{MODULE}}' => $moduleName,
            '{{MODULE_LOWER}}' => Str::lower($moduleName),
            '{{MODULE_NAMESPACE}}' => trim($modulesNamespace, '\\'),
            '{{PROVIDER}}' => $moduleName . 'ServiceProvider',
        ];

        foreach (self::DIRECTORIES as $directory) {
            $this->files->ensureDirectoryExists($modulePath . DIRECTORY_SEPARATOR . $directory);
        }

        $written = 0;

        foreach (self::FILES as $relativePath => $stub) {
            $target = $modulePath . DIRECTORY_SEPARATOR . str_replace('MODULE', $moduleName, $relativePath);
            $source = rtrim($stubPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $stub;

            if ($this->files->exists($target) && ! $force) {
                continue;
            }

            $contents = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $this->files->get($source),
            );
            $this->files->put($target, $contents);
            $written++;
        }

        return $written;
    }
}
