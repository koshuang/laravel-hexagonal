<?php

namespace Koshuang\LaravelHexagonal\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ValidateCommand extends Command
{
    /** @var string */
    protected $signature = 'hexagonal:validate';

    /** @var string */
    protected $description = 'Validate module dependency direction with Deptrac';

    public function handle(): int
    {
        $configPath = config('hexagonal.deptrac.config');
        $binaryPath = config('hexagonal.deptrac.binary');
        $timeout = config('hexagonal.deptrac.timeout', 120);

        if (! is_string($configPath) || ! is_string($binaryPath) || ! is_int($timeout)) {
            $this->error('Invalid Hexagonal architecture validation configuration.');

            return self::FAILURE;
        }

        if (! is_file($configPath)) {
            $this->error("Deptrac configuration not found: {$configPath}");
            $this->line('Run "php artisan hexagonal:install" first.');

            return self::FAILURE;
        }

        if (! is_file($binaryPath)) {
            $this->error("Deptrac executable not found: {$binaryPath}");
            $this->line('Run "composer update nwidart/laravel-modules deptrac/deptrac" to install architecture dependencies.');

            return self::FAILURE;
        }

        $process = new Process([
            $binaryPath,
            'analyze',
            '--config-file=' . $configPath,
        ], base_path(), null, null, $timeout);
        $exitCode = $process->run();
        $this->output->write($process->getOutput());
        $this->output->write($process->getErrorOutput());

        if ($exitCode !== self::SUCCESS) {
            $this->error('Hexagonal architecture validation failed.');
        }

        return $exitCode;
    }
}
