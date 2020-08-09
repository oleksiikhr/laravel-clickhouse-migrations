<?php

declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations;

use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseMigrationContract;
use ClickHouseDB\Client;

abstract class ClickhouseMigration implements ClickhouseMigrationContract
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $database;

    public function __construct(?Client $client = null, ?string $database = null)
    {
        $this->client = $client ?? app('clickhouse');
        $this->database = $database ?? config('clickhouse.config.options.database');
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->database;
    }

    /**
     * @param  string  $database
     * @return $this
     */
    public function setDatabaseName(string $database): self
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param  Client  $client
     * @return $this
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
