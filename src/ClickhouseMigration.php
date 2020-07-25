<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations;

use ClickHouseDB\Client;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseMigrationContract;

abstract class ClickhouseMigration implements ClickhouseMigrationContract
{
    /**
     * @var Client
     */
    public $client;

    public function __construct(Clickhouse $client)
    {
        $this->client = $client->getClient();
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return config('clickhouse.config.options.database');
    }
}
