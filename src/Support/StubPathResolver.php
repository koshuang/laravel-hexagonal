<?php

declare(strict_types=1);

namespace Koshuang\LaravelHexagonal\Support;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

final readonly class StubPathResolver
{
    public function __construct(private Filesystem $files)
    {
    }

    public function module(string $customPath = ''): string
    {
        if ($customPath !== '') {
            return $this->resolveCustomDirectory($customPath);
        }

        return $this->resolveDirectory('module');
    }

    public function shared(): string
    {
        return $this->resolveDirectory('shared');
    }

    public function deptrac(): string
    {
        $configured = config('hexagonal.stubs.deptrac');

        if (is_string($configured) && $this->files->isFile($configured)) {
            return $configured;
        }

        return $this->packagePath('stubs/deptrac.yaml');
    }

    private function resolveDirectory(string $stub): string
    {
        $configured = config("hexagonal.stubs.{$stub}");

        if (is_string($configured) && $this->files->isDirectory($configured)) {
            return $configured;
        }

        return $this->packagePath("stubs/{$stub}");
    }

    private function packagePath(string $path): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $path;
    }

    private function resolveCustomDirectory(string $path): string
    {
        $resolved = is_dir($path) ? $path : base_path($path);

        if (! is_dir($resolved)) {
            throw new RuntimeException("Module stub directory does not exist: {$path}");
        }

        return $resolved;
    }
}
