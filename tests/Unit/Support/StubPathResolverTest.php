<?php

declare(strict_types=1);

namespace Koshuang\LaravelHexagonal\Tests\Unit\Support;

use Illuminate\Filesystem\Filesystem;
use Koshuang\LaravelHexagonal\Support\StubPathResolver;
use Koshuang\LaravelHexagonal\Tests\TestCase;

final class StubPathResolverTest extends TestCase
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

    public function test_it_uses_a_published_module_stub_directory(): void
    {
        $files = new Filesystem();
        $publishedPath = $this->fixturePath . '/module';
        $files->ensureDirectoryExists($publishedPath);
        config()->set('hexagonal.stubs.module', $publishedPath);

        $this->assertSame($publishedPath, new StubPathResolver($files)->module());
    }

    public function test_it_accepts_a_custom_module_stub_directory_for_one_command(): void
    {
        $files = new Filesystem();
        $customPath = $this->fixturePath . '/custom-module';
        $files->ensureDirectoryExists($customPath);

        $this->assertSame($customPath, new StubPathResolver($files)->module($customPath));
    }

    public function test_it_falls_back_to_the_package_module_stub_directory(): void
    {
        $files = new Filesystem();
        config()->set('hexagonal.stubs.module', $this->fixturePath . '/missing-module');

        $stubPath = new StubPathResolver($files)->module();

        $this->assertDirectoryExists($stubPath);
        $this->assertStringEndsWith('/stubs/module', $stubPath);
    }
}
