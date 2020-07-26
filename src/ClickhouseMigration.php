<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations;

use ClickHouseDB\Client;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseMigrationContract;

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

    public function __construct(Clickhouse $client, ?string $database = null)
    {
        $this->client = $client->getClient();
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
