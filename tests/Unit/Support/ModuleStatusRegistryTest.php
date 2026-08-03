<?php

declare(strict_types=1);

namespace Koshuang\LaravelHexagonal\Tests\Unit\Support;

use Illuminate\Filesystem\Filesystem;
use Koshuang\LaravelHexagonal\Support\ModuleStatusRegistry;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ModuleStatusRegistryTest extends TestCase
{
    private string $fixturePath;

    private Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturePath = sys_get_temp_dir() . '/laravel-hexagonal-status-' . bin2hex(random_bytes(6));
        $this->files = new Filesystem();
        $this->files->ensureDirectoryExists($this->fixturePath);
    }

    protected function tearDown(): void
    {
        $this->files->deleteDirectory($this->fixturePath);

        parent::tearDown();
    }

    private function statusesPath(): string
    {
        return $this->fixturePath . '/modules_statuses.json';
    }

    public function test_it_creates_the_statuses_file_with_a_new_active_module(): void
    {
        $registry = new ModuleStatusRegistry($this->files, $this->statusesPath());

        $registry->setActive('Order', true);

        $this->assertFileExists($this->statusesPath());
        $this->assertSame(
            ['Order' => true],
            json_decode((string) $this->files->get($this->statusesPath()), true),
        );
    }

    public function test_it_preserves_unknown_existing_entries_when_merging(): void
    {
        $this->files->put(
            $this->statusesPath(),
            json_encode(['Account' => true, 'Shared' => true], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
        );

        $registry = new ModuleStatusRegistry($this->files, $this->statusesPath());
        $registry->setActive('Order', true);

        $this->assertSame(
            ['Account' => true, 'Shared' => true, 'Order' => true],
            json_decode((string) $this->files->get($this->statusesPath()), true),
        );
    }

    public function test_it_can_disable_a_module_without_losing_other_entries(): void
    {
        $this->files->put(
            $this->statusesPath(),
            json_encode(['Account' => true, 'Order' => true], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
        );

        $registry = new ModuleStatusRegistry($this->files, $this->statusesPath());
        $registry->setActive('Order', false);

        $this->assertSame(
            ['Account' => true, 'Order' => false],
            json_decode((string) $this->files->get($this->statusesPath()), true),
        );
    }

    public function test_it_rejects_an_invalid_statuses_file(): void
    {
        $this->files->put($this->statusesPath(), 'not-json');

        $this->expectException(RuntimeException::class);

        (new ModuleStatusRegistry($this->files, $this->statusesPath()))->setActive('Order', true);
    }
}
