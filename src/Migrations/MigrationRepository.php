<?php

declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use ClickHouseDB\Client;
use ClickHouseDB\Statement;

class MigrationRepository
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
     * Creating a new table to store migrations.
     *
     * @return Statement
     */
    public function create(): Statement
    {
        return $this->client->write('
            CREATE TABLE IF NOT EXISTS {table} (
                migration String,
                batch UInt32,
                applied_at DateTime DEFAULT NOW()
            )
            ENGINE = ReplacingMergeTree()
            ORDER BY migration
        ', [
            'table' => $this->table,
        ]);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $rows = $this->client->select('SELECT migration FROM {table}', [
            'table' => $this->table,
        ])->rows();

        return collect($rows)->pluck('migration')->all();
    }

    /**
     * Get latest accepted migrations.
     *
     * @return array
     */
    public function latest(): array
    {
        $rows = $this->client->select('
            SELECT migration
            FROM {table}
            ORDER BY batch DESC, migration DESC
        ', [
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
            ->select('SELECT MAX(batch) AS batch FROM {table}', ['table' => $this->table])
            ->fetchOne('batch');
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
        return $this->client->write('ALTER TABLE {table} DELETE WHERE migration=:migration', [
            'table' => $this->table,
            'migration' => $migration,
        ]);
    }

    /**
     * @return int
     */
    public function total(): int
    {
        return (int) $this->client->select('SELECT COUNT(*) AS count FROM {table}', [
            'table' => $this->table,
        ])->fetchOne('count');
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return (bool) $this->client->select('EXISTS TABLE {table}', [
            'table' => $this->table,
        ])->fetchOne('result');
    }

    /**
     * @param  string  $migration
     * @return array|null
     */
    public function find(string $migration): ?array
    {
        return $this->client->select('SELECT * FROM {table} WHERE migration=:migration LIMIT 1', [
            'table' => $this->table,
            'migration' => $migration,
        ])->fetchOne();
    }
}
