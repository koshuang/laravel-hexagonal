<?php

namespace Koshuang\LaravelHexagonal\Support;

use Illuminate\Filesystem\Filesystem;

/**
 * Renders the publishable Deptrac template into a per-project `deptrac.yaml`.
 *
 * The template ships a complete, valid ruleset (covering `./app` and
 * `./Modules`, with Carbon + Illuminate allowances). This renderer makes the
 * allowances *configurable*: an application that does not want a given
 * framework allowance can disable it in `config/hexagonal.php` without
 * hand-editing the generated YAML.
 *
 * Disabling a layer removes both its multi-line collector block and every
 * `- <Layer>` rule referencing it, so the emitted file never references an
 * undefined layer.
 */
class DeptracFileRenderer
{
    public function __construct(
        private readonly Filesystem $files,
        private readonly string $templatePath,
    ) {
    }

    public function render(
        bool $allowCarbon = true,
        bool $allowIlluminateSupportFacades = true,
        bool $allowIlluminate = true,
    ): string {
        $content = rtrim($this->files->get($this->templatePath), "\n") . "\n";

        if (! $allowCarbon) {
            $content = $this->removeLayer($content, 'Carbon');
        }

        if (! $allowIlluminateSupportFacades) {
            $content = $this->removeLayer($content, 'IlluminateSupportFacade');
        }

        if (! $allowIlluminate) {
            $content = $this->removeLayer($content, 'Illuminate');
        }

        return $content;
    }

    private function removeLayer(string $content, string $layer): string
    {
        // Remove the whole multi-line layer definition block (its leading
        // `-` list item plus every indented collector line below it).
        $pattern = '/^    -\n      name: ' . preg_quote($layer, '/') . '\n(?:      .*\n)*/m';
        $content = preg_replace($pattern, '', $content) ?? $content;

        // Remove every `- <Layer>` reference from the ruleset sections.
        $content = preg_replace('/^ {6}- ' . preg_quote($layer, '/') . '\n/m', '', $content) ?? $content;

        return $content;
    }
}
