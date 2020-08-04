<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

use ClickHouseDB\Client;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseCase;
use Alexeykhr\ClickhouseMigrations\Clickhouse;

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
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $content = require __DIR__.'/../config/clickhouse.php';

        $app->config->set(['clickhouse' => $content]);
    }
}
