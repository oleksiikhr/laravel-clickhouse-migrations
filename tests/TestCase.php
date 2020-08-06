<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

use ClickHouseDB\Client;
use Orchestra\Testbench\TestCase as BaseCase;
use Alexeykhr\ClickhouseMigrations\Clickhouse;
use Alexeykhr\ClickhouseMigrations\Providers\MigrationProvider;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationRepository;

class TestCase extends BaseCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDatabase();
    }

    /**
     * @return void
     */
    protected function refreshDatabase(): void
    {
        $client = $this->getClient();
        $client->database('system');
        $client->write("DROP DATABASE IF EXISTS default");
        $client->write("CREATE DATABASE default");
        $client->database('default');
    }

    /**
     * @return Client
     */
    protected function getClient(): Client
    {
        static $clickhouse;

        return $clickhouse ?? $clickhouse = (new Clickhouse([
            'host' => 'localhost',
            'port' => 8123,
            'username' => 'default',
            'password' => '',
            'options' => [
                'database' => 'default',
                'timeout' => 1,
                'connectTimeOut' => 2,
            ],
        ]))->getClient();
    }

    /**
     * @return MigrationRepository
     */
    protected function repository(): MigrationRepository
    {
        return new MigrationRepository('migrations', $this->getClient());
    }

    /**
     * @param  string  $path
     * @return string
     */
    protected function assetsPath(string $path = ''): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'assets'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * @inheritDoc
     */
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $content = require __DIR__.'/../config/clickhouse.php';

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
