<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use ClickHouseDB\Client;
use ClickHouseDB\Statement;

class MigrationModel
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(string $table, Client $client)
    {
        $this->table = $table;
        $this->client = $client;
    }

    /**
     * Creating a new table to store migrations
     *
     * @return Statement
     */
    public function create(): Statement
    {
        return $this->client->write("
            CREATE TABLE IF NOT EXISTS {table} (
                migration String,
                batch UInt32
            )
            ENGINE = ReplacingMergeTree()
            ORDER BY migration
        ", [
            'table' => $this->table,
        ]);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $rows = $this->client->select("SELECT migration FROM {table}", [
            'table' => $this->table,
        ])->rows();

        return collect($rows)->pluck('migration')->all();
    }

    /**
     * Get the last migration batch
     *
     * @return array
     */
    public function getLast(): array
    {
        $rows = $this->client->select("
            SELECT migration
            FROM {table}
            WHERE batch = (
                SELECT MAX(batch)
                FROM {table}
            )
            ORDER BY migration DESC
        ", [
            'table' => $this->table,
        ])->rows();

        return collect($rows)->pluck('migration')->all();
    }

    /**
     * @return int
     */
    public function getNextBatchNumber(): int
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * @return int
     */
    public function getLastBatchNumber(): int
    {
        return $this->client
            ->select("SELECT MAX(batch) AS batch FROM {table}", ['table' => $this->table])
            ->fetchOne()['batch'];
    }

    /**
     * @param  string  $migration
     * @param  int  $batch
     * @return Statement
     */
    public function add(string $migration, int $batch): Statement
    {
        return $this->client->insert($this->table, [[$migration, $batch]], ['migration', 'batch']);
    }

    /**
     * @param  string  $migration
     * @return Statement
     */
    public function delete(string $migration): Statement
    {
        return $this->client->write("ALTER TABLE {table} DELETE WHERE migration=:migration", [
            'table' => $this->table,
            'migration' => $migration,
        ]);
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return (bool) $this->client->write("EXISTS TABLE {table}", [
            'table' => $this->table,
        ])
            ->fetchOne()['result'];
    }
}
