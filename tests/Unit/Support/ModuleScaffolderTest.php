<?php

declare(strict_types=1);

namespace Koshuang\LaravelHexagonal\Tests\Unit\Support;

use Illuminate\Filesystem\Filesystem;
use Koshuang\LaravelHexagonal\Support\ModuleScaffolder;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ModuleScaffolderTest extends TestCase
{
    private string $fixturePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturePath = sys_get_temp_dir() . '/laravel-hexagonal-' . bin2hex(random_bytes(6));
    }

    protected function tearDown(): void
    {
        new Filesystem()->deleteDirectory($this->fixturePath);

        parent::tearDown();
    }

    public function test_it_generates_a_module_with_all_layers(): void
    {
        $scaffolder = new ModuleScaffolder(new Filesystem());

        $written = $scaffolder->scaffold(
            'order',
            $this->fixturePath,
            'Modules',
            dirname(__DIR__, 3) . '/stubs/module',
        );

        $this->assertSame(8, $written);
        $this->assertFileExists($this->fixturePath . '/Order/module.json');
        $this->assertDirectoryExists($this->fixturePath . '/Order/Application/Port/In');
        $this->assertStringContainsString(
            'namespace Modules\\Order\\Infrastructure\\Providers;',
            (string) file_get_contents($this->fixturePath . '/Order/Infrastructure/Providers/OrderServiceProvider.php'),
        );
    }

    public function test_it_rejects_a_module_name_that_can_escape_the_modules_directory(): void
    {
        $this->expectException(RuntimeException::class);

        new ModuleScaffolder(new Filesystem())->scaffold(
            '../outside',
            $this->fixturePath,
            'Modules',
            dirname(__DIR__, 3) . '/stubs/module',
        );
    }
}
