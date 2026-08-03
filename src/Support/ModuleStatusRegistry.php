<?php

namespace Koshuang\LaravelHexagonal\Support;

use Illuminate\Filesystem\Filesystem;

/**
 * Reads and writes the nwidart/laravel-modules `modules_statuses.json` file.
 *
 * The file is a flat map of module name => boolean activation status
 * (e.g. `{"Account": true, "Shared": true}`). New modules added by
 * `hexagonal:make-module` must be enabled here or nwidart will treat them as
 * disabled. This registry merges the new entry into the existing map,
 * preserving any unknown entries already present in the file.
 */
class ModuleStatusRegistry
{
    public function __construct(
        private readonly Filesystem $files,
        private readonly string $statusesPath,
    ) {
    }

    /**
     * Set the activation status for a single module, preserving all other
     * entries currently present in the statuses file.
     */
    public function setActive(string $module, bool $active): void
    {
        $statuses = $this->readStatuses();
        $statuses[$module] = $active;

        $this->writeStatuses($statuses);
    }

    /**
     * @return array<string, bool>
     */
    private function readStatuses(): array
    {
        if (! $this->files->exists($this->statusesPath)) {
            return [];
        }

        /** @var mixed $decoded */
        $decoded = json_decode((string) $this->files->get($this->statusesPath), true);

        if (! is_array($decoded)) {
            throw new \RuntimeException("Invalid module statuses file: {$this->statusesPath}");
        }

        foreach ($decoded as $key => $status) {
            if (! is_string($key) || ! is_bool($status)) {
                throw new \RuntimeException("Invalid module statuses file: {$this->statusesPath}");
            }
        }

        /** @var array<string, bool> $statuses */
        $statuses = $decoded;

        return $statuses;
    }

    /**
     * @param array<string, bool> $statuses
     */
    private function writeStatuses(array $statuses): void
    {
        $encoded = json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($encoded === false) {
            throw new \RuntimeException("Unable to encode module statuses file: {$this->statusesPath}");
        }

        $this->files->put($this->statusesPath, $encoded);
    }
}
