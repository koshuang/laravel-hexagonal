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
}
