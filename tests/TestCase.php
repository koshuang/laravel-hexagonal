<?php

declare(strict_types=1);

namespace Koshuang\LaravelHexagonal\Tests;

use Koshuang\LaravelHexagonal\LaravelHexagonalServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelHexagonalServiceProvider::class,
        ];
    }
}
