<?php

declare(strict_types=1);

namespace Koshuang\LaravelHexagonal\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Koshuang\LaravelHexagonal\Tests\TestCase;

final class MakeModuleCommandTest extends TestCase
{
    private string $fixturePath;

    private Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturePath = sys_get_temp_dir() . '/laravel-hexagonal-command-' . bin2hex(random_bytes(6));
        $this->files = new Filesystem();
        $this->files->ensureDirectoryExists($this->fixturePath);

        config()->set('hexagonal.modules_path', $this->fixturePath . '/Modules');
        config()->set('hexagonal.modules_namespace', 'Modules');
        config()->set('hexagonal.modules_statuses', $this->fixturePath . '/modules_statuses.json');
    }

    protected function tearDown(): void
    {
        $this->files->deleteDirectory($this->fixturePath);

        parent::tearDown();
    }

    public function test_it_creates_module_and_enables_it_in_statuses(): void
    {
        $this->runArtisan(['name' => 'Order'], 0);

        $this->assertDirectoryExists($this->fixturePath . '/Modules/Order');
        $this->assertSame(
            ['Order' => true],
            json_decode((string) $this->files->get($this->fixturePath . '/modules_statuses.json'), true),
        );
    }

    public function test_no_active_option_disables_the_module_in_statuses(): void
    {
        $this->runArtisan(['name' => 'Order', '--no-active' => true], 0);

        $this->assertSame(
            ['Order' => false],
            json_decode((string) $this->files->get($this->fixturePath . '/modules_statuses.json'), true),
        );
    }

    public function test_it_preserves_existing_statuses_when_creating_a_module(): void
    {
        $this->files->put(
            $this->fixturePath . '/modules_statuses.json',
            $this->encode(['Account' => true, 'Shared' => true]),
        );

        $this->runArtisan(['name' => 'Order'], 0);

        $this->assertSame(
            ['Account' => true, 'Shared' => true, 'Order' => true],
            json_decode((string) $this->files->get($this->fixturePath . '/modules_statuses.json'), true),
        );
    }

    public function test_it_rejects_a_duplicate_module_name_without_force(): void
    {
        $this->files->ensureDirectoryExists($this->fixturePath . '/Modules/Order');

        $this->runArtisan(['name' => 'Order'], 1);
    }

    public function test_force_option_allows_overwriting_an_existing_module(): void
    {
        $this->files->ensureDirectoryExists($this->fixturePath . '/Modules/Order');
        $this->files->put($this->fixturePath . '/Modules/Order/module.json', 'stale');

        $this->runArtisan(['name' => 'Order', '--force' => true], 0);

        $this->assertStringContainsString('"name": "Order"', (string) $this->files->get($this->fixturePath . '/Modules/Order/module.json'));
    }

    public function test_it_rejects_an_invalid_module_name(): void
    {
        $this->runArtisan(['name' => '../outside'], 1);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function runArtisan(array $parameters, int $expectedExitCode): void
    {
        $command = $this->artisan('hexagonal:make-module', $parameters);

        if (is_int($command)) {
            $this->assertSame($expectedExitCode, $command);

            return;
        }

        $command->assertExitCode($expectedExitCode);
    }

    /**
     * @param array<string, bool> $statuses
     */
    private function encode(array $statuses): string
    {
        return (string) json_encode($statuses, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }
}
