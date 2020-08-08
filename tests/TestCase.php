<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

use Orchestra\Testbench\TestCase as BaseCase;
use Alexeykhr\ClickhouseMigrations\Providers\MigrationProvider;

class TestCase extends BaseCase
{
    use InteractsWithAssets, InteractsWithClickhouse;

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $content = require __DIR__.'/../config/clickhouse.php';
        $content['migrations']['path'] = $this->dynamicPath('migrations');

        $app->config->set(['clickhouse' => $content]);
    }

    /**
     * @inheritDoc
     */
    protected function getPackageProviders($app): array
    {
        return [
            MigrationProvider::class,
        ];
    }
}
