<?php

declare(strict_types=1);

namespace Koshuang\LaravelHexagonal\Tests\Unit\Support;

use Illuminate\Filesystem\Filesystem;
use Koshuang\LaravelHexagonal\Support\DeptracFileRenderer;
use Koshuang\LaravelHexagonal\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class DeptracFileRendererTest extends TestCase
{
    private Filesystem $files;

    private string $templatePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        $this->templatePath = sys_get_temp_dir() . '/laravel-hexagonal-deptrac-' . bin2hex(random_bytes(6)) . '.yaml';
    }

    protected function tearDown(): void
    {
        $this->files->delete($this->templatePath);

        parent::tearDown();
    }

    public function test_default_render_keeps_app_and_modules_paths(): void
    {
        $this->files->put($this->templatePath, $this->template());

        $rendered = $this->renderer()->render();

        $this->assertStringContainsString('- ./app', $rendered);
        $this->assertStringContainsString('- ./Modules', $rendered);
    }

    public function test_default_render_keeps_carbon_and_illuminate_allowances(): void
    {
        $this->files->put($this->templatePath, $this->template());

        $rendered = $this->renderer()->render();

        $this->assertStringContainsString('name: Carbon', $rendered);
        $this->assertStringContainsString('- Carbon', $rendered);
        $this->assertStringContainsString('name: IlluminateSupportFacade', $rendered);
        $this->assertStringContainsString('- IlluminateSupportFacade', $rendered);
    }

    public function test_disabling_carbon_removes_layer_definition_and_rules(): void
    {
        $this->files->put($this->templatePath, $this->template());

        $rendered = $this->renderer()->render(allowCarbon: false);

        $this->assertStringNotContainsString('name: Carbon', $rendered);
        $this->assertStringNotContainsString('- Carbon', $rendered);
        $this->assertStringNotContainsString('Carbon\\', $rendered);
    }

    public function test_disabling_illuminate_support_facades_removes_layer_definition_and_rules(): void
    {
        $this->files->put($this->templatePath, $this->template());

        $rendered = $this->renderer()->render(allowIlluminateSupportFacades: false);

        $this->assertStringNotContainsString('name: IlluminateSupportFacade', $rendered);
        $this->assertStringNotContainsString('- IlluminateSupportFacade', $rendered);
    }

    public function test_disabling_illuminate_removes_layer_definition_and_rules(): void
    {
        $this->files->put($this->templatePath, $this->template());

        $rendered = $this->renderer()->render(allowIlluminate: false);

        $this->assertDoesNotMatchRegularExpression('/^      name: Illuminate$/m', $rendered);
        $this->assertDoesNotMatchRegularExpression('/^      - Illuminate$/m', $rendered);
    }

    #[DataProvider('disabledAllowanceProvider')]
    public function test_rendered_yaml_stays_balanced(bool $allowCarbon, bool $allowIlluminateSupportFacades): void
    {
        $this->files->put($this->templatePath, $this->template());

        $rendered = $this->renderer()->render($allowCarbon, $allowIlluminateSupportFacades);

        // Removing a layer must not leave dangling ruleset references, which
        // would make deptrac fail with an "unknown layer" error.
        foreach (['Carbon', 'IlluminateSupportFacade'] as $layer) {
            if ($layer === 'Carbon' && ! $allowCarbon) {
                $this->assertDoesNotMatchRegularExpression('/- ' . $layer . '/', $rendered);
            }
            if ($layer === 'IlluminateSupportFacade' && ! $allowIlluminateSupportFacades) {
                $this->assertDoesNotMatchRegularExpression('/- ' . $layer . '/', $rendered);
            }
        }
    }

    /**
     * @return array<string, array{bool, bool}>
     */
    public static function disabledAllowanceProvider(): array
    {
        return [
            'no carbon' => [false, true],
            'no facades' => [true, false],
            'neither' => [false, false],
        ];
    }

    private function renderer(): DeptracFileRenderer
    {
        return new DeptracFileRenderer($this->files, $this->templatePath);
    }

    private function template(): string
    {
        return <<<'YAML'
deptrac:
  paths:
    - ./app
    - ./Modules
  ignore_uncovered_internal_classes: true
  layers:
    -
      name: App
      collectors:
        -
          type: classLike
          value: .*App\\.*
    -
      name: Illuminate
      collectors:
        -
          type: classLike
          value: .*Illuminate\\(?!Support).*
    -
      name: IlluminateSupportFacade
      collectors:
        -
          type: classLike
          value: .*Illuminate\\Support\\Facades\\.*
    -
      name: IlluminateSupport
      collectors:
        -
          type: classLike
          value: .*Illuminate\\Support\\(?!Facades).*
    -
      name: Carbon
      collectors:
        -
          type: classLike
          value: .*Carbon\\.*
  ruleset:
    App:
      - Illuminate
      - IlluminateSupportFacade
      - IlluminateSupport
      - Carbon
YAML;
    }
}
