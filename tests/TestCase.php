<?php

declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

use Alexeykhr\ClickhouseMigrations\Providers\MigrationProvider;
use Alexeykhr\ClickhouseMigrations\Tests\Asserts\AssertsClickhouse;
use Orchestra\Testbench\TestCase as BaseCase;

class TestCase extends BaseCase
{
    use InteractsWithAssets, InteractsWithClickhouse, AssertsClickhouse;

    /**
     * {@inheritdoc}
     */
    protected function setUpTraits(): array
    {
        $uses = parent::setUpTraits();

        if (isset($uses[InteractsWithAssets::class])) {
            $this->refreshDynamic();
        }

        if (isset($uses[InteractsWithClickhouse::class])) {
            $this->refreshClickhouse();
        }

        return $uses;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $content = require __DIR__.'/../config/clickhouse.php';
        $content['migrations']['path'] = $this->dynamicPath('migrations');

        $app->config->set(['clickhouse' => $content]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [
            MigrationProvider::class,
        ];
    }
}
