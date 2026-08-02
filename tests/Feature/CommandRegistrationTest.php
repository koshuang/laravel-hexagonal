<?php

declare(strict_types=1);

namespace Koshuang\LaravelHexagonal\Tests\Feature;

use Koshuang\LaravelHexagonal\Tests\TestCase;

final class CommandRegistrationTest extends TestCase
{
    public function test_install_command_is_registered(): void
    {
        $command = $this->artisan('hexagonal:install', ['--help']);

        if (is_int($command)) {
            $this->assertSame(0, $command);

            return;
        }

        $command->assertExitCode(0);
    }

    public function test_make_module_command_is_registered(): void
    {
        $command = $this->artisan('hexagonal:make-module', ['--help']);

        if (is_int($command)) {
            $this->assertSame(0, $command);

            return;
        }

        $command->assertExitCode(0);
    }

    public function test_validate_command_is_registered(): void
    {
        $command = $this->artisan('hexagonal:validate', ['--help']);

        if (is_int($command)) {
            $this->assertSame(0, $command);

            return;
        }

        $command->assertExitCode(0);
    }

    public function test_validate_command_fails_when_deptrac_is_not_installed(): void
    {
        config()->set('hexagonal.deptrac.config', sys_get_temp_dir() . '/missing-deptrac.yaml');
        config()->set('hexagonal.deptrac.binary', sys_get_temp_dir() . '/missing-deptrac');

        $command = $this->artisan('hexagonal:validate');

        if (is_int($command)) {
            $this->assertSame(1, $command);

            return;
        }

        $command->assertExitCode(1);
    }
}
