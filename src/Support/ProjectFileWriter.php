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

        $composer = $this->readComposer($composerPath);
        $currentModulesPath = $this->readModulesAutoload($composer);

        if ($currentModulesPath === 'Modules/') {
            return false;
        }

        if ($currentModulesPath !== null && ! $force) {
            throw new RuntimeException(
                "The Modules\\ autoload entry already points to {$currentModulesPath}. Use --force to replace it.",
            );
        }

        $this->writeComposer($composerPath, $this->withModulesAutoload($composer));

        return true;
    }

    public function addDevDependency(
        string $composerPath,
        string $package,
        string $constraint,
        bool $force = false,
    ): bool {
        if (! $this->files->exists($composerPath)) {
            throw new RuntimeException("Composer file not found: {$composerPath}.");
        }

        $composer = $this->readComposer($composerPath);
        $requireDev = $composer['require-dev'] ?? [];
        $requireDev = is_array($requireDev) ? $requireDev : [];
        $currentConstraint = $requireDev[$package] ?? null;

        if ($currentConstraint !== null && ! is_string($currentConstraint)) {
            throw new RuntimeException("The {$package} dev dependency must contain a string constraint.");
        }

        if ($currentConstraint === $constraint) {
            return false;
        }

        if ($currentConstraint !== null && ! $force) {
            throw new RuntimeException(
                "The {$package} dev dependency already uses {$currentConstraint}. Use --force to replace it.",
            );
        }

        $requireDev[$package] = $constraint;
        ksort($requireDev);
        $composer['require-dev'] = $requireDev;
        $this->writeComposer($composerPath, $composer);

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function readComposer(string $composerPath): array
    {
        $decodedComposer = json_decode($this->files->get($composerPath), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decodedComposer)) {
            throw new RuntimeException("Composer file must contain a JSON object: {$composerPath}.");
        }

        /** @var array<string, mixed> $decodedComposer */
        return $decodedComposer;
    }

    /**
     * @param array<string, mixed> $composer
     */
    private function readModulesAutoload(array $composer): ?string
    {
        $autoload = $composer['autoload'] ?? [];
        $psr4 = is_array($autoload) ? ($autoload['psr-4'] ?? []) : [];
        $currentModulesPath = is_array($psr4) ? ($psr4['Modules\\'] ?? null) : null;

        if ($currentModulesPath !== null && ! is_string($currentModulesPath)) {
            throw new RuntimeException('The Modules\\ autoload entry must contain a string path.');
        }

        return $currentModulesPath;
    }

    /**
     * @param array<string, mixed> $composer
     *
     * @return array<string, mixed>
     */
    private function withModulesAutoload(array $composer): array
    {
        $autoload = $composer['autoload'] ?? [];
        $autoload = is_array($autoload) ? $autoload : [];
        $psr4 = $autoload['psr-4'] ?? [];
        $psr4 = is_array($psr4) ? $psr4 : [];
        $psr4['Modules\\'] = 'Modules/';
        $autoload['psr-4'] = $psr4;
        $composer['autoload'] = $autoload;

        return $composer;
    }

    /**
     * @param array<string, mixed> $composer
     */
    private function writeComposer(string $composerPath, array $composer): void
    {
        $updated = json_encode(
            $composer,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ) . PHP_EOL;

        $this->files->put($composerPath, $updated);
    }
}
