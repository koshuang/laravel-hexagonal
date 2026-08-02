<?php

declare(strict_types=1);

namespace Koshuang\LaravelHexagonal\Tests\Unit\Support;

use Illuminate\Filesystem\Filesystem;
use Koshuang\LaravelHexagonal\Support\ProjectFileWriter;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ProjectFileWriterTest extends TestCase
{
    private string $fixturePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturePath = sys_get_temp_dir() . '/laravel-hexagonal-' . bin2hex(random_bytes(6));
        new Filesystem()->ensureDirectoryExists($this->fixturePath);
    }

    protected function tearDown(): void
    {
        new Filesystem()->deleteDirectory($this->fixturePath);

        parent::tearDown();
    }

    public function test_it_adds_the_modules_psr4_mapping_once(): void
    {
        $files = new Filesystem();
        $composerPath = $this->fixturePath . '/composer.json';
        $files->put($composerPath, json_encode([
            'autoload' => [
                'psr-4' => [
                    'App\\' => 'app/',
                ],
            ],
        ], JSON_THROW_ON_ERROR));
        $writer = new ProjectFileWriter($files);

        $this->assertTrue($writer->addModulesAutoload($composerPath));
        $this->assertFalse($writer->addModulesAutoload($composerPath));
        /** @var array{autoload: array{'psr-4': array<string, string>}} $composer */
        $composer = json_decode($files->get($composerPath), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('Modules/', $composer['autoload']['psr-4']['Modules\\']);
    }

    public function test_it_does_not_replace_an_existing_modules_mapping_without_force(): void
    {
        $files = new Filesystem();
        $composerPath = $this->fixturePath . '/composer.json';
        $files->put($composerPath, json_encode([
            'autoload' => [
                'psr-4' => [
                    'Modules\\' => 'app/Modules/',
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $this->expectException(RuntimeException::class);

        new ProjectFileWriter($files)->addModulesAutoload($composerPath);
    }
}
