<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

use Orchestra\Testbench\TestCase;
use Alexeykhr\ClickhouseMigrations\MigrationProvider;

class JustCaseTest extends TestCase
{
    public function testAsd(): void
    {
        $this->assertTrue(true);
    }

    protected function getPackageProviders($app)
    {
        return [
            MigrationProvider::class,
        ];
    }
}
