<?php

namespace Koshuang\LaravelHexagonal\Support;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class ProjectFileWriter
{
    public function __construct(private readonly Filesystem $files)
    {
    }

    public function copyFile(string $source, string $destination, bool $force = false): bool
    {
        if ($this->files->exists($destination) && ! $force) {
            return false;
        }

        $this->files->ensureDirectoryExists(dirname($destination));

        if (! $this->files->copy($source, $destination)) {
            throw new RuntimeException("Unable to copy {$source} to {$destination}.");
        }

        return true;
    }

    public function copyDirectory(string $source, string $destination, bool $force = false): int
    {
        $copied = 0;

        foreach ($this->files->allFiles($source) as $file) {
            $relativePath = $file->getRelativePathname();
            $target = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($this->copyFile($file->getPathname(), $target, $force)) {
                $copied++;
            }
        }

        return $copied;
    }

    public function addModulesAutoload(string $composerPath, bool $force = false): bool
    {
        if (! $this->files->exists($composerPath)) {
            throw new RuntimeException("Composer file not found: {$composerPath}.");
        }

        $contents = $this->files->get($composerPath);
        $composer = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        $autoload = $composer['autoload']['psr-4'] ?? [];

        if (isset($autoload['Modules\\']) && ! $force) {
            return false;
        }

        $composer['autoload']['psr-4']['Modules\\'] = 'Modules/';

        $updated = json_encode(
            $composer,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ) . PHP_EOL;

        $this->files->put($composerPath, $updated);

        return true;
    }
}
